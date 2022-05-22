<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Filter\Entity\TeamMember;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Leon\BswBundle\Repository\BswWorkTaskTrailRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use MathPHP\Algebra;

/**
 * @property Session $session
 * @property Expr    $expr
 */
trait Common
{
    /**
     * @var bool
     */
    protected $isTeamTask = false;

    /**
     * bootstrap
     */
    public function bootstrap()
    {
        parent::bootstrap();

        $this->isTeamTask = $this->getArgs('type') === 'team';
    }

    /**
     * @param bool $leader
     *
     * @return array
     * @throws
     */
    protected function weightTypeArgs(bool $leader): array
    {
        $maxDay = $leader ? $this->cnf->work_lifecycle_max_day_by_leader : $this->cnf->work_lifecycle_max_day;

        $weekendDays = floor($maxDay / 7) * 2;
        $workDays = $maxDay - $weekendDays;
        $maxHours = ceil($workDays * $this->cnf->work_lifecycle_day_hours);

        if ($maxHours > $max = 176) { // 22 work days in month
            $maxHours = $max;
        }

        $step = 1;
        $factors = Algebra::factors($maxHours);
        foreach ($factors as $f) {
            if ($maxHours / $f <= 5) {
                $step = $f;
                break;
            }
        }

        $marks = range($step, $maxHours - $step, $step);
        $marks = array_combine($marks, $marks);

        return ['min' => 1, 'max' => $maxHours, 'marks' => $marks];
    }

    /**
     * Trail logger
     *
     * @param Arguments $args
     * @param string    $trail
     * @param bool      $reliable
     *
     * @return bool|Error
     * @throws
     */
    protected function trailLogger(Arguments $args, string $trail, bool $reliable = false)
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $result = $trailRepo->newly(
            [
                'userId'   => $this->usr('usr_uid'),
                'taskId'   => intval($args->newly ? $args->result : $args->original['id']),
                'reliable' => $reliable ? 1 : 0,
                'trail'    => Html::cleanHtml($trail),
            ]
        );

        if ($result === false) {
            return new ErrorDbPersistence();
        }

        return true;
    }

    /**
     * Get work task team info
     *
     * @return array
     */
    protected function workTaskTeam(): array
    {
        return [$this->usr('usr_team'), $this->usr('usr_team_leader')];
    }

    /**
     * Get work task team and leader info
     *
     * @return array
     * @throws
     */
    protected function workTaskTeamAndLeader(): array
    {
        $leaderId = $leaderTg = 0;
        [$team, $leader] = $this->workTaskTeam();

        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);
        $teamLeader = $adminRepo->findOneBy(
            [
                'teamId'     => $team,
                'teamLeader' => 1,
            ]
        );
        if ($teamLeader) {
            $leaderId = $teamLeader->id;
            $leaderTg = $teamLeader->telegramId;
        }

        return [$team, $leader, $leaderId, $leaderTg];
    }

    /**
     * Get team default value
     *
     * @return string
     */
    public function teamDefaultValue(): ?string
    {
        [$team, $leader] = $this->workTaskTeam();
        if (!$team) {
            return null;
        }

        if ($leader) {
            return "{$team}";
        }

        // return "{$team}-{$this->usr('usr_uid')}";
        return "{$team}";
    }

    /**
     * Get admin by id
     *
     * @param int $userId
     *
     * @return mixed
     * @throws
     */
    protected function getUserById(int $userId)
    {
        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);

        return $adminRepo->find($userId);
    }

    /**
     * List task trail
     *
     * @param int $taskId
     *
     * @return array
     * @throws
     */
    protected function listTaskTrail(int $taskId): array
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $list = $trailRepo->lister(
            [
                'limit'  => 0,
                'alias'  => 'tt',
                'select' => ['u.name', 'tt.id', 'tt.reliable', 'tt.trail', 'tt.addTime AS time'],
                'join'   => [
                    'u' => [
                        'entity' => BswAdminUser::class,
                        'left'   => ['tt.userId'],
                        'right'  => ['u.id'],
                    ],
                ],
                'where'  => [
                    $this->expr->eq('tt.taskId', ':task'),
                    $this->expr->eq('tt.state', ':state'),
                ],
                'args'   => [
                    'task'  => [$taskId],
                    'state' => [Abs::NORMAL],
                ],
                'order'  => ['tt.id' => Abs::SORT_ASC],
            ]
        );

        return $this->taskTrailHandler($list);
    }

    /**
     * Task trail handler
     *
     * @param array $list
     *
     * @return array
     * @throws
     */
    protected function taskTrailHandler(array $list): array
    {
        foreach ($list as &$item) {
            if (isset($item['type']) && $item['type'] === 2) {
                $item['name'] = '★';
            }

            $item['human'] = $this->humanTimeDiff($item['time']);
            [$item['name'], $item['color']] = $this->nameToColor($item['name']);
            $item['time'] = date('m/d H:i', strtotime($item['time']));

            // mentions
            $member = $this->matchMentions(Html::cleanHtml($item['trail']));
            foreach ($member as $v) {
                $name = Html::tag('a', "@{$v['name']}", ['href' => 'javascript:;']);
                $item['trail'] = str_replace($v['block'], $name, $item['trail']);
            }

            // links
            preg_match_all('/https?\:\/\/[\S]+/i', $item['trail'], $result);
            foreach ($result[0] ?? [] as $link) {
                $linkHtml = Html::tag('a', $link, ['href' => $link, 'target' => '_blank']);
                $item['trail'] = str_replace($link, $linkHtml, $item['trail']);
            }
        }

        return $list;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraUserId(Arguments $args): array
    {
        [$team] = $this->workTaskTeam();

        if ($team) {
            $filter = [
                'where' => [$this->expr->eq('kvp.teamId', ':team')],
                'args'  => ['team' => [$team]],
            ];
        } else {
            $filter = [
                'where' => [$this->expr->gt('kvp.teamId', ':team')],
                'args'  => ['team' => [0]],
            ];
        }

        $teamFilter = [
            'join' => [
                'bwt' => [
                    'entity' => BswWorkTeam::class,
                    'left'   => ['kvp.teamId'],
                    'right'  => ['bwt.id'],
                ],
            ],
        ];

        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);

        return $adminRepo->kvp(
            ['kvp.name', 'bwt.name AS team'],
            Abs::PK,
            function ($v) {
                return $v['name'];
            },
            $filter,
            $teamFilter
        );
    }

    /**
     * Get team member
     *
     * @param int $teamId
     *
     * @return array
     * @throws
     */
    protected function getTeamMemberMap(?int $teamId = null): array
    {
        $teamFilter = [];
        if ($teamId) {
            $teamFilter = [
                'where' => [$this->expr->eq('u.teamId', ':teamId')],
                'args'  => ['teamId' => [$teamId]],
            ];
        }

        /**
         * @var BswAdminUserRepository $userRepo
         */
        $userRepo = $this->repo(BswAdminUser::class);

        return $userRepo->filters($teamFilter)->lister(
            [
                'limit'  => 0,
                'alias'  => 'u',
                'select' => [
                    'u.teamId',
                    't.name AS teamName',
                    'u.id AS memberId',
                    'u.name AS memberName',
                ],
                'join'   => [
                    't' => [
                        'entity' => BswWorkTeam::class,
                        'left'   => ['u.teamId'],
                        'right'  => ['t.id'],
                    ],
                ],
                'where'  => [
                    $this->expr->gt('u.teamId', ':team'),
                    $this->expr->eq('u.state', ':state'),
                ],
                'args'   => [
                    'team'  => [0],
                    'state' => [Abs::NORMAL],
                ],
            ]
        );
    }

    /**
     * Get team member
     *
     * @param int $teamId
     *
     * @return array
     */
    protected function getTeamMemberTree(?int $teamId = null): array
    {
        static $tree;

        if (isset($tree)) {
            return $tree;
        }

        $tree = [];
        $teamMember = $this->getTeamMemberMap($teamId);

        foreach ($teamMember as $item) {
            $tt = $item['teamId'];
            $mm = $item['memberId'];
            if (!isset($tree[$tt])) {
                $tree[$tt] = [
                    'title'    => $item['teamName'],
                    'value'    => $tt,
                    'children' => [],
                ];
            }
            if (!empty($mm) && !isset($tree[$tt]['children'][$mm])) {
                $tree[$tt]['children'][$mm] = [
                    'title'    => $item['memberName'],
                    'value'    => "{$tt}-{$mm}",
                    'children' => [],
                ];
            }
        }

        $tree = Helper::arrayValues(
            $tree,
            function ($value) {
                return is_numeric(key($value));
            }
        );

        return $tree;
    }

    /**
     * Correct team member filter
     *
     * @param string $field
     * @param array  $alias
     * @param array  $condition
     *
     * @return array
     */
    protected function correctTeamMemberFilter(string $field, array $alias, ?array $condition = null): array
    {
        $condition = $condition ?? [];
        [$team] = $this->workTaskTeam();
        if (!$team) {
            return $condition;
        }

        $filter = $condition[$field]['filter'] ?? null;
        $value = $condition[$field]['value'] ?? null;

        if (!$filter) {
            $filter = (new TeamMember())->setAlias($alias);
        }

        $value = $filter->correctTeamMemberByAgency($team, $value);
        $condition[$field] = $this->createFilter($filter, $value);

        return $condition;
    }

    /**
     * Send telegram tips
     *
     * @param bool   $isTelegramId
     * @param int    $id // User id or Telegram id
     * @param string $messageLabel
     * @param array  $messageArgs
     * @param string $route
     */
    public function sendTelegramTips(
        bool $isTelegramId,
        int $id,
        string $messageLabel,
        array $messageArgs = [],
        ?string $route = null
    ) {
        if (!$this->cnf->work_task_send_telegram) {
            return;
        }

        if ($isTelegramId) {
            $telegramId = $id;
        } else {
            $telegramId = $this->getUserById($id)->telegramId;
        }

        if (!$telegramId) {
            return;
        }

        $route = $route ?: $this->cnf->route_default;
        $key = $this->parameter('token_for_login_key');

        $url = $this->url($route, [$key => $this->createSceneToken(1, $telegramId)]);
        $url = "[Doorway]({$url})";

        $member = $this->usr('usr_account');
        $message = $this->messageLang(
            $messageLabel,
            array_merge(['{{ member }}' => $member], $messageArgs)
        );

        $this->telegramSendMessage($telegramId, "\[{$url}] {$message}");
    }

    /**
     * Match mentions
     *
     * @param string $content
     *
     * @return array
     */
    protected function matchMentions(string $content): array
    {
        $member = [];
        preg_match_all('/@(.*?)\!(\d+)/', $content, $result);
        foreach ($result[0] ?? [] as $key => $block) {
            $member[] = [
                'block'      => $block,
                'telegramId' => $result[2][$key],
                'name'       => $result[1][$key],
            ];
        }

        return $member;
    }

    /**
     * @return array
     */
    protected function operatesButton(): array
    {
        [$team, $leader] = $this->workTaskTeam();
        $style = $team ? ['margin' => '3px 4px 3px 0', 'float' => Abs::POS_LEFT] : [];

        $current = function (string $route, ?bool $isTeamTask = null): string {
            $currentPage = $this->route === $route;
            if (isset($this->isTeamTask) && isset($isTeamTask) && $this->isTeamTask !== $isTeamTask) {
                $currentPage = false;
            }

            return $currentPage ? Abs::THEME_BSW_DARK : Abs::THEME_BSW_LIGHT;
        };

        return [
            (new Button('Member task list'))
                ->setType($current('app_bsw_work_task_preview', false))
                ->setRoute('app_bsw_work_task_preview')
                ->setIcon('b:icon-box')
                ->setStyle($style)
                ->setArgs(['type' => 'member']),

            (new Button('Team task list'))
                ->setType($current('app_bsw_work_task_preview', true))
                ->setRoute('app_bsw_work_task_preview')
                ->setIcon('b:icon-similarproduct')
                ->setStyle($style)
                ->setArgs(['type' => 'team']),

            (new Button('New task'))
                ->setRoute('app_bsw_work_task_persistence')
                ->setType($current('app_bsw_work_task_persistence'))
                ->setIcon($this->cnf->icon_newly)
                ->setStyle($style)
                ->setDisplay($leader),

            (new Button('Weekly publication'))
                ->setType($current('app_bsw_work_week_report'))
                ->setRoute('app_bsw_work_week_report')
                ->setIcon('b:icon-calendar')
                ->setStyle($style),

            (new Button('Progress chart'))
                ->setType($current('app_bsw_work_task_overall'))
                ->setIcon('a:line-chart')
                ->setClick('showResult')
                ->setStyle($style)
                ->setArgs(
                    [
                        'status'   => Abs::RESULT_STATUS_404,
                        'title'    => $this->twigLang('Look forward'),
                        'subTitle' => 'Gradually improving, look forward.',
                    ]
                ),

            (new Button('New task'))
                ->setType(Abs::THEME_BSW_SUCCESS)
                ->setRoute('app_bsw_work_task_simple')
                ->setIcon('a:bug')
                ->setDisplay($team)
                ->setClick('showIFrame')
                ->setName('new_work_task')
                ->setStyle(['margin' => '3px 0 3px 4px'])
                ->setArgs(
                    [
                        'width'  => Abs::MEDIA_SM,
                        'height' => 410,
                        'title'  => $this->twigLang('New task'),
                    ]
                ),

            (new Button('Logout'))
                ->setType(Abs::THEME_LINK)
                ->setRoute($this->cnf->route_logout)
                ->setIcon($this->cnf->icon_logout)
                ->setDisplay($team)
                ->setStyle(['margin' => '3px 0 3px 4px'])
                ->setConfirm($this->messageLang('Are you sure')),
        ];
    }

    /**
     * Before action logic
     */
    public function beforeLogic()
    {
        [$_, $leader] = $this->workTaskTeam();

        $title = $this->twigLang('Work task manager');
        $this->seoTitle = $title;

        $leader = $leader ? ' 🚩' : null;
        $this->cnf->copyright = "{$title} © {$this->usr('usr_account')}{$leader}";
        $this->logicMerge('display', [Abs::MODULE_MENU, Abs::MODULE_HEADER, Abs::MODULE_CRUMBS]);
    }
}