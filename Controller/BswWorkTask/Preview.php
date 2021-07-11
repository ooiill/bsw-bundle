<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Entity\Accurate;
use Leon\BswBundle\Module\Filter\Entity\Senior;
use Leon\BswBundle\Module\Filter\Entity\TeamMember;
use Leon\BswBundle\Module\Filter\Entity\WeekIntersect;
use Leon\BswBundle\Module\Form\Entity\SelectTree;
use Leon\BswBundle\Module\Form\Entity\Week;
use Leon\BswBundle\Module\Scene\Charm;
use Leon\BswBundle\Repository\BswWorkTaskTrailRepository;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait Preview
{
    /**
     * @var array
     */
    protected $previewAlias = [
        'team'   => ['bau', 'teamId'],
        'member' => ['bwt', 'userId'],
    ];

    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswWorkTask::class;
    }

    /**
     * @return array
     */
    public function previewFilterAnnotation(): array
    {
        [$team] = $this->workTaskTeam();

        return [
            'userId' => false,
            'team'   => $this->isTeamTask ? false : [
                'label'      => 'User id',
                'field'      => 'bwt.userId',
                'type'       => SelectTree::class,
                'typeArgs'   => ['expandAll' => true],
                'enum'       => $this->getTeamMemberTree($team),
                'enumExtra'  => false,
                'filter'     => TeamMember::class,
                'filterArgs' => ['alias' => $this->previewAlias],
                'value'      => $this->teamDefaultValue(),
                'column'     => 3,
                'sort'       => 1,
            ],
            'week'   => [
                'label'      => 'Week n',
                'field'      => 'bwt.addTime',
                'type'       => Week::class,
                'filter'     => WeekIntersect::class,
                'filterArgs' => [
                    'timestamp' => true,
                    'carryTime' => false,
                    'alias'     => ['from' => 'bwt.startTime', 'to' => 'bwt.endTime'],
                ],
                'sort'       => 3,
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewQuery(): array
    {
        if ($this->isTeamTask) {
            $order = [
                'bwt.id' => Abs::SORT_DESC,
            ];
        } else {
            $order = [
                'bwt.userId' => Abs::SORT_ASC,
                'bwt.id'     => Abs::SORT_ASC,
            ];
        }

        return [
            'limit'  => 100,
            'select' => ['bwt'],
            'join'   => [
                'bau' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['bwt.userId'],
                    'right'  => ['bau.id'],
                ],
            ],
            'order'  => $order,
        ];
    }

    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'userId'    => !$this->isTeamTask,
            'weight'    => !$this->isTeamTask,
            'trail'     => [
                'width' => 120,
                'align' => Abs::POS_CENTER,
                'sort'  => 6.1,
                'html'  => true,
            ],
            Abs::TR_ACT => [
                'width' => 156,
                'align' => Abs::POS_LEFT,
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewFilterCorrect(Arguments $args): array
    {
        if (empty($args->condition['bwt.state'])) {
            $args->condition['bwt.state'] = $this->createFilter(Senior::class, [Senior::GT, [0]]);
        }

        $args->condition = $this->correctTeamMemberFilter(
            'bwt.userId',
            $this->previewAlias,
            $args->condition ?? null
        );

        $args->condition['bwt.type'] = $this->createFilter(Accurate::class, $this->isTeamTask ? 2 : 1);

        return [$args->filter, $args->condition];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return $this->operatesButton();
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        [$team, $leader] = $this->workTaskTeam();
        $isMyTask = $args->item['userId'] === $this->usr('usr_uid');

        return [
            (new Button('Progress'))
                ->setType(Abs::THEME_BSW_SUCCESS)
                ->setRoute('app_bsw_work_task_progress')
                ->setIcon('b:icon-process')
                ->setClick('showIFrame')
                ->setName('adjust_work_progress')
                ->setDisabled(!$isMyTask)
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 428,
                        'title'  => false,
                    ]
                ),

            (new Button('Edit record'))
                ->setRoute('app_bsw_work_task_persistence')
                ->setIcon('b:icon-edit')
                ->setDisplay($leader)
                ->setArgs(['id' => $args->item['id']]),

            (new Button('Notes'))
                ->setRoute('app_bsw_work_task_notes')
                ->setIcon('b:icon-form')
                ->setClick('showIFrame')
                ->setName('note_work_task')
                ->setArgs(
                    [
                        'fill'   => ['taskId' => $args->item['id']],
                        'type'   => $this->isTeamTask ? 'team' : 'member',
                        'width'  => $this->isTeamTask ? Abs::MEDIA_SM : 500,
                        'height' => $this->isTeamTask ? 435 : 315,
                        'title'  => false,
                    ]
                ),

            (new Button('Transfer'))
                ->setRoute('app_bsw_work_task_transfer')
                ->setIcon('b:icon-feng')
                ->setDisplay($leader && !$this->isTeamTask)
                ->setClick('showIFrame')
                ->setName('transfer_work_task')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => Abs::MEDIA_XS,
                        'height' => 222,
                        'title'  => false,
                    ]
                ),

            (new Button('Weight'))
                ->setType(Abs::THEME_DEFAULT)
                ->setRoute('app_bsw_work_task_weight')
                ->setIcon('b:icon-jewelry')
                ->setDisplay($leader && !$this->isTeamTask)
                ->setClick('showIFrame')
                ->setName('adjust_work_weight')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => 500,
                        'height' => 255,
                        'title'  => false,
                    ]
                ),

            (new Button('Close'))
                ->setType(Abs::THEME_DANGER)
                ->setRoute('app_bsw_work_task_close')
                ->setIcon('b:icon-success')
                ->setDisplay($leader)
                ->setDisabled(!in_array($args->item['state'], [3, 4]))
                ->setClick('requestByAjax')
                ->setConfirm($this->messageLang('Are you sure'))
                ->setArgs(
                    [
                        'id'      => $args->item['id'],
                        'refresh' => true,
                    ]
                ),
        ];
    }

    /**
     * @param Arguments $args
     * @param array     $cnfLeft
     * @param array     $cnfRight
     *
     * @return Charm
     */
    public function chartTime(Arguments $args, array $cnfLeft, array $cnfRight)
    {
        if (!in_array($args->item['state'], [1, 2])) {
            return new Charm(Abs::HTML_CODE, $args->value);
        }

        [$gap, $tip] = Helper::gapDateDetail(
            $args->value,
            [
                'year'   => $this->fieldLang('Year'),
                'month'  => $this->fieldLang('Month'),
                'day'    => $this->fieldLang('Day'),
                'hour'   => $this->fieldLang('Hour'),
                'minute' => $this->fieldLang('Minute'),
                'second' => null,
            ]
        );

        $html = Abs::HTML_NORMAL_TEXT . Abs::LINE_DASHED;

        if ($gap >= 0) {
            $left = $this->fieldLang($cnfLeft['lang']);
            $html .= $this->useTpl($cnfLeft['tpl'], "{$left}: {$tip}");
            if (!empty($cnfRight['infect'])) {
                $html .= $this->getUpwardInfectHtml($cnfRight['infect'], 3);
            }
        } else {
            $right = $this->fieldLang($cnfRight['lang']);
            $html .= $this->useTpl($cnfRight['tpl'], "{$right}: {$tip}");
            if (!empty($cnfRight['infect'])) {
                $html .= $this->getUpwardInfectHtml($cnfRight['infect'], 3);
            }
        }

        return new Charm($html, $args->value);
    }

    /**
     * @param Arguments $args
     *
     * @return Charm
     */
    public function previewCharmStartTime(Arguments $args)
    {
        return $this->chartTime(
            $args,
            ['lang' => 'Ready', 'tpl' => Abs::HTML_GREEN_TEXT],
            ['lang' => 'Consumed', 'tpl' => Abs::HTML_ORANGE_TEXT]
        );
    }

    /**
     * @param Arguments $args
     *
     * @return Charm
     */
    public function previewCharmEndTime(Arguments $args)
    {
        return $this->chartTime(
            $args,
            ['lang' => 'Surplus', 'tpl' => Abs::HTML_GREEN],
            ['lang' => 'Expired', 'tpl' => Abs::HTML_RED]
        );
    }

    /**
     * @param Arguments $args
     *
     * @return Charm
     */
    public function previewCharmTitle(Arguments $args)
    {
        /**
         * @var BswWorkTaskTrailRepository $trailRepo
         */
        $trailRepo = $this->repo(BswWorkTaskTrail::class);
        $trail = $trailRepo->findOneBy(
            ['taskId' => $args->item['id'], 'reliable' => 1],
            ['id' => Abs::SORT_DESC]
        );

        if ($trail) {
            $tips = '↑' . $this->humanTimeDiff($trail->addTime);
            $gap = Helper::gapDateTime($trail->addTime, date(Abs::FMT_FULL)) / Abs::TIME_HOUR;
            if ($gap > Abs::HEX_HOUR_DAY * 3) {
                $tips = $this->useTpl(Abs::BSW_RED_SMALL, $tips);
            } else {
                $tips = $this->useTpl(Abs::BSW_GREEN_SMALL, $tips);
            }
        } else {
            $tips = '↑Nil';
            $tips = $this->useTpl(Abs::BSW_ORANGE_SMALL, $tips);
        }

        return new Charm('{value}', "{$args->value} {$tips}");
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        $button = (new Button('lifecycle'))
            ->setType(Abs::THEME_DEFAULT)
            ->setSize(Abs::SIZE_SMALL)
            ->setClick('trailDrawerShow')
            ->setArgs(['id' => $args->original['id']]);

        $args->hooked['originalTitle'] = $args->hooked['title'];
        $args->hooked['trail'] = $this->getButtonHtml($button);
        $args->hooked['trailList'] = $this->listTaskTrail($args->original['id']);

        if (in_array($args->hooked['state'], [3, 4])) {
            $args->hooked[Abs::TAG_ROW_CLS_NAME] = 'bsw-row-status-green';
        }

        return $args->hooked;
    }

    /**
     * Preview record
     *
     * @Route("/bsw-work-task/preview", name="app_bsw_work_task_preview")
     * @Access(export=true)
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview(
            [
                'dynamic'     => 10,
                'filterJump'  => true,
                'pageJump'    => true,
                'afterModule' => [
                    'drawer' => function ($logic, $args) {
                        $trailVisible = array_column($args['preview']['list'], 'id');
                        $trailVisible = Helper::arrayValuesSetTo($trailVisible, false, true);
                        $trailVisible = Helper::jsonStringify($trailVisible);

                        return compact('trailVisible');
                    },
                ],
            ],
            [],
            'task/preview.html'
        );
    }
}