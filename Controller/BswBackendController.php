<?php

namespace Leon\BswBundle\Controller;

use Doctrine\ORM\AbstractQuery;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\Traits as CT;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Entity\BswAdminLogin;
use Leon\BswBundle\Entity\BswAdminPersistenceLog;
use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Bsw as BswModule;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Hook\Entity\Aes;
use Leon\BswBundle\Module\Hook\Entity\Enums;
use Leon\BswBundle\Module\Hook\Entity\EnumTrans;
use Leon\BswBundle\Module\Hook\Entity\FieldsTrans;
use Leon\BswBundle\Module\Hook\Entity\HourDuration;
use Leon\BswBundle\Module\Hook\Entity\MessagesTrans;
use Leon\BswBundle\Module\Hook\Entity\SeoTrans;
use Leon\BswBundle\Module\Hook\Entity\Timestamp;
use Leon\BswBundle\Module\Hook\Entity\TwigTrans;
use Leon\BswBundle\Module\Scene\Links;
use Leon\BswBundle\Module\Scene\Menu;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Scene\Setting;
use Leon\BswBundle\Repository\BswAdminLoginRepository;
use Leon\BswBundle\Repository\BswAdminPersistenceLogRepository;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Leon\BswBundle\Repository\BswAttachmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class BswBackendController extends BswWebController
{
    use CT\BackendEntityHint,
        CT\BackendPreset;

    /**
     * @var array
     */
    protected $bsw;

    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_BACKEND;

    /**
     * @var bool
     */
    protected $bswSrc = true;

    /**
     * @var string
     */
    protected $skUser = 'backend-user-sk';

    /**
     * @var string
     */
    protected $skCnf = 'backend-cnf-sk';

    /**
     * @var bool
     */
    protected $plaintextSensitive = false;

    /**
     * @var string
     */
    protected $twigBlank = Abs::BACKEND_TWIG_BLANK;

    /**
     * @var string
     */
    protected $twigEmpty = Abs::BACKEND_TWIG_EMPTY;

    /**
     * @var string
     */
    protected $twigPreview = Abs::BACKEND_TWIG_PREVIEW;

    /**
     * @var string
     */
    protected $twigPersistence = Abs::BACKEND_TWIG_PERSISTENCE;

    /**
     * @var string
     */
    protected $twigChart = Abs::BACKEND_TWIG_CHART;

    /**
     * @var array
     */
    protected $currentSrcCss = [
        'bsw' => Abs::CSS_BSW,
    ];

    /**
     * @var array
     */
    protected $currentSrcJs = [
        'fulls' => Abs::JS_FULL_SCREEN,
        'copy'  => Abs::JS_COPY,
        'bsw'   => Abs::JS_BSW,
    ];

    /**
     * Bootstrap
     *
     * @throws
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        if ($this->bswSrc) {
            $lang = $this->langLatest($this->langMap, 'en');
            $this->appendSrcJsWithKey('lang', Abs::JS_LANG[$lang], Abs::POS_TOP, 'bsw', true);
            $this->appendSrcJsWithKey('moment-lang', Abs::JS_MOMENT_LANG[$lang], Abs::POS_TOP, 'bsw', true);
        }

        if ($this->env === 'dev') {

            $this->mapCdnSrcCss = [];
            $this->mapCdnSrcJs = [];

            if (isset($this->initialSrcJs[$key = 'ant-d'])) {
                $this->appendSrcJsWithKey($key, Abs::JS_ANT_D_LANG, Abs::POS_TOP);
            }
            if (isset($this->initialSrcJs[$key = 'vue'])) {
                $this->appendSrcJsWithKey($key, Abs::JS_VUE, Abs::POS_TOP);
            }
        }

        $this->appendSrcCssWithKey('ant-d', $this->cnf->theme, Abs::POS_TOP);
    }

    /**
     * @param array $fileCnf
     *
     * @return array
     */
    protected function extraConfig(array $fileCnf): array
    {
        $dbCnf = $this->getDbConfig('app_database_config');
        $sessionCnf = $this->session->get($this->skCnf) ?? [];

        $pair = array_merge($fileCnf, $dbCnf, $sessionCnf);
        $pair = Helper::numericValues($pair);

        return $pair;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function hookerExtraArgs(array $args = []): array
    {
        return Helper::merge(
            [
                Aes::class           => [
                    'aes_iv'     => $this->parameter('aes_iv'),
                    'aes_key'    => $this->parameter('aes_key'),
                    'aes_method' => $this->parameter('aes_method'),
                    'plaintext'  => $this->plaintextSensitive,
                ],
                Timestamp::class     => [
                    'persistence_newly_empty' => time(),
                ],
                HourDuration::class  => [
                    'digit' => [
                        'year'  => $this->fieldLang('Year'),
                        'month' => $this->fieldLang('Month'),
                        'day'   => $this->fieldLang('Day'),
                        'hour'  => $this->fieldLang('Hour'),
                    ],
                ],
                Enums::class         => [
                    'trans' => $this->translator,
                ],
                MessagesTrans::class => [
                    'trans' => $this->translator,
                ],
                TwigTrans::class     => [
                    'trans' => $this->translator,
                ],
                FieldsTrans::class   => [
                    'trans' => $this->translator,
                ],
                EnumTrans::class     => [
                    'trans' => $this->translator,
                ],
                SeoTrans::class      => [
                    'trans' => $this->translator,
                ],
            ],
            $args
        );
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error|Response
     * @throws
     */
    protected function webShouldAuth(array $args)
    {
        /**
         * Telegram token mode
         */
        $tk4l = $this->parameter('token_for_login_key');
        $token = $this->getArgs($tk4l);
        if ($token && is_string($token) && $record = $this->checkSceneToken($token, 1)) {
            $this->session->clear();
            if ($record instanceof Error) {
                return $record;
            }

            /**
             * @var BswAdminUserRepository $adminRepo
             */
            $adminRepo = $this->repo(BswAdminUser::class);
            $user = $adminRepo->lister(
                [
                    'limit' => 1,
                    'where' => [
                        $this->expr->eq('bau.state', ':state'),
                        $this->expr->eq('bau.telegramId', ':telegram'),
                    ],
                    'args'  => [
                        'state'    => [Abs::NORMAL],
                        'telegram' => [$record->userId],
                    ],
                ],
                false,
                AbstractQuery::HYDRATE_OBJECT
            );

            if ($user) {
                $args = $this->getArgs();
                Helper::arrayPop($args, [$tk4l]);
                $this->loginAdminUser($user, $this->getClientIp());

                return $this->redirectToRoute($this->route, $args);
            }
        }

        /**
         * Normal mode
         */
        $user = $this->session->get($this->skUser);
        if (empty($user)) {
            return new ErrorAuthorization();
        }

        $strictIp = $this->parameter('backend_with_ip_strict');
        $userIp = $user[$this->cnf->usr_ip] ?? false;

        if ($strictIp && ($this->getClientIp() !== $userIp)) {
            $this->session->clear();
            $this->logger->error("Account login in another place: {$user[$this->cnf->usr_uid]} at {$userIp}");

            return new ErrorAuthorization();
        }

        return $user;
    }

    /**
     * Strict login
     *
     * @return bool
     * @throws
     */
    protected function strictAuthorization(): bool
    {
        if (!$this->usr) {
            return false;
        }

        $session = $this->repo(BswAdminUser::class)->lister(
            [
                'limit'  => 1,
                'alias'  => 'u',
                'select' => ['u', 'log.addTime AS lastLoginTime'],
                'join'   => [
                    'log' => [
                        'entity' => BswAdminLogin::class,
                        'left'   => ['u.id'],
                        'right'  => ['log.userId'],
                    ],
                ],
                'where'  => [$this->expr->eq('u.id', ':uid')],
                'args'   => ['uid' => [$this->usr('usr_uid')]],
                'order'  => ['log.id' => Abs::SORT_DESC],
            ]
        );

        $strict = [
            'updateTime'    => $this->usr('usr_update'),
            'lastLoginTime' => $this->usr('usr_login'),
        ];

        if (!$this->parameter('backend_with_login_log')) {
            unset($strict['lastLoginTime']);
        }

        if (!$this->parameter('backend_maintain_alone')) {
            $strict = [];
        }

        foreach ($strict as $from => $to) {
            if ($session[$from] != $to) {
                $this->session->clear();
                $this->logger->error("Account login by another: {$this->usr('usr_uid')} at {$this->usr('usr_login')}");

                return false;
            }
        }

        return true;
    }

    /**
     * Access builder
     *
     * @param object $usr
     *
     * @return array
     */
    protected function accessBuilder($usr): array
    {
        $auto = [];
        $all = $this->getAccessOfAll();
        foreach ($all as $route => $item) {
            if ($item['export']) {
                $auto[$route] = true;
            }
        }

        $route = $this->getRouteOfAll(true);
        $route = array_merge($auto, $route);

        if ($this->root($usr)) {
            return $route;
        }

        $render = Helper::arrayValuesSetTo($all, false);
        $user = $this->getAccessOfUserWithRole($usr->{$this->cnf->usr_uid});
        $access = array_merge($route, $render, $user);

        foreach ($render as $key => $value) {
            if (!isset($all[$key])) {
                continue;
            }
            if ($all[$key]['same']) {
                $access[$key] = $access[$all[$key]['same']];
            }
            if ($all[$key]['join'] === false) {
                $access[$key] = true;
            }
        }

        return $access;
    }

    /**
     * Get modules for blank
     *
     * @return array
     */
    protected function blankModule(): array
    {
        return [
            BswModule\Menu\Module::class    => ['sort' => Abs::MODULE_MENU_SORT],
            BswModule\Header\Module::class  => ['sort' => Abs::MODULE_HEADER_SORT],
            BswModule\Crumbs\Module::class  => ['sort' => Abs::MODULE_CRUMBS_SORT, 'crumbs' => $this->crumbs],
            BswModule\Tabs\Module::class    => ['sort' => Abs::MODULE_TABS_SORT],
            BswModule\Welcome\Module::class => ['sort' => Abs::MODULE_WELCOME_SORT],
            BswModule\Operate\Module::class => ['sort' => Abs::MODULE_OPERATE_SORT],
            BswModule\Footer\Module::class  => ['sort' => Abs::MODULE_FOOTER_SORT],
            BswModule\Modal\Module::class   => ['sort' => Abs::MODULE_MODAL_SORT],
            BswModule\Drawer\Module::class  => ['sort' => Abs::MODULE_DRAWER_SORT],
            BswModule\Result\Module::class  => ['sort' => Abs::MODULE_RESULT_SORT],
        ];
    }

    /**
     * Render blank
     *
     * @param string|null $view
     * @param array       $args
     * @param array       $moduleList
     *
     * @return Response|array
     * @throws
     */
    protected function showBlank(?string $view = null, array $args = [], array $moduleList = []): Response
    {
        $args = array_merge(
            $args,
            [
                'scene'        => Abs::TAG_BLANK,
                'twigBsw'      => $this->twigPath(Abs::BACKEND_TWIG_BLANK),
                'twigBswForce' => $this->twigPath(Abs::BACKEND_TWIG_BLANK, true),
                'twigApp'      => $this->twigPath($this->twigBlank),
            ]
        );

        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Filter\Module::class => ['sort' => Abs::MODULE_FILTER_SORT],
                BswModule\Data\Module::class   => ['sort' => Abs::MODULE_DATA_SORT],
            ]
        );

        return $this->showModule($moduleList, $view, $args);
    }

    /**
     * Render empty
     *
     * @param string|null $view
     * @param array       $args
     * @param array       $moduleList
     *
     * @return Response|array
     * @throws
     */
    protected function showEmpty(?string $view = null, array $args = [], array $moduleList = []): Response
    {
        $args = array_merge(
            $args,
            [
                'scene'        => Abs::TAG_EMPTY,
                'twigBsw'      => $this->twigPath(Abs::BACKEND_TWIG_EMPTY),
                'twigBswForce' => $this->twigPath(Abs::BACKEND_TWIG_EMPTY, true),
                'twigApp'      => $this->twigPath($this->twigEmpty),
                'display'      => ['menu', 'header', 'crumbs', 'footer'],
            ]
        );

        return $this->showBlank($view, $args, $moduleList);
    }

    /**
     * Render preview
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showPreview(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        $args = array_merge(
            $args,
            [
                'scene'        => Abs::TAG_PREVIEW,
                'twigBsw'      => $this->twigPath(Abs::BACKEND_TWIG_PREVIEW),
                'twigBswForce' => $this->twigPath(Abs::BACKEND_TWIG_PREVIEW, true),
                'twigApp'      => $this->twigPath($this->twigPreview),
            ]
        );

        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Filter\Module::class  => ['sort' => Abs::MODULE_FILTER_SORT],
                BswModule\Data\Module::class    => ['sort' => Abs::MODULE_DATA_SORT],
                BswModule\Preview\Module::class => ['sort' => Abs::MODULE_PREVIEW_SORT],
            ]
        );

        return $this->showModule($moduleList, $view ?? $this->twigPreview, $args);
    }

    /**
     * Render persistence
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showPersistence(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        if (!isset($args['submit'])) {
            $args['submit'] = $this->postArgs('submit', false) ?? [];
        }

        $args = array_merge(
            $args,
            [
                'scene'        => Abs::TAG_PERSISTENCE,
                'twigBsw'      => $this->twigPath(Abs::BACKEND_TWIG_PERSISTENCE),
                'twigBswForce' => $this->twigPath(Abs::BACKEND_TWIG_PERSISTENCE, true),
                'twigApp'      => $this->twigPath($this->twigPersistence),
            ]
        );

        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Data\Module::class        => ['sort' => Abs::MODULE_DATA_SORT],
                BswModule\Persistence\Module::class => ['sort' => Abs::MODULE_PERSISTENCE_SORT],
            ]
        );

        return $this->showModule($moduleList, $view ?? $this->twigPersistence, $args);
    }

    /**
     * Render chart
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showChart(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        $args = array_merge(
            $args,
            [
                'scene'        => Abs::TAG_CHART,
                'twigBsw'      => $this->twigPath(Abs::BACKEND_TWIG_CHART),
                'twigBswForce' => $this->twigPath(Abs::BACKEND_TWIG_CHART, true),
                'twigApp'      => $this->twigPath($this->twigChart),
            ]
        );

        $moduleList = Helper::merge(
            $this->blankModule(),
            $moduleList,
            [
                BswModule\Filter\Module::class => ['sort' => Abs::MODULE_FILTER_SORT],
                BswModule\Data\Module::class   => ['sort' => Abs::MODULE_DATA_SORT],
                BswModule\Chart\Module::class  => ['sort' => Abs::MODULE_CHART_SORT],
            ]
        );

        return $this->showModule($moduleList, $view ?? $this->twigChart, $args);
    }

    /**
     * Render away without view
     *
     * @param array $args
     * @param array $relation
     * @param bool  $responseWhenMessage
     *
     * @return Response|Message|array
     * @throws
     */
    protected function doAway(array $args = [], array $relation = [], bool $responseWhenMessage = true)
    {
        $args['relation'] = $relation;
        $moduleList = [
            BswModule\Menu\Module::class   => ['sort' => Abs::MODULE_MENU_SORT],
            BswModule\Crumbs\Module::class => ['sort' => Abs::MODULE_CRUMBS_SORT, 'crumbs' => $this->crumbs],
            BswModule\Away\Module::class   => ['sort' => Abs::MODULE_AWAY_SORT],
        ];

        return $this->showModule($moduleList, null, $args, $responseWhenMessage, true);
    }

    /**
     * Get access of render
     *
     * @return array
     */
    public function getAccessOfRender(): array
    {
        $access = $this->getAccessOfAll(true, $this->bsw['menu']);
        $annotation = [];

        foreach ($access as $key => $item) {
            $enum = [];
            foreach ($item['items'] as $route => $target) {
                if ($target['join'] === false || $target['same']) {
                    continue;
                }
                $enum[$route] = $target['info'];
            }

            if (!isset($annotation[$key])) {
                $annotation[$key] = [
                    'info'  => $item['info'] ?: 'UnSetDescription',
                    'type'  => new Checkbox(),
                    'enum'  => [],
                    'value' => [],
                ];
            }

            $annotation[$key]['enum'] = array_merge($annotation[$key]['enum'], $enum);
        }

        return $annotation;
    }

    /**
     * Get access of role
     *
     * @param int $roleId
     *
     * @return array
     * @throws
     */
    public function getAccessOfRole(int $roleId = null): array
    {
        $roleId = $roleId ?? $this->usr('usr_role');
        if (empty($roleId)) {
            return [];
        }

        $role = $this->repo(BswAdminRole::class)->find($roleId);
        if (!$role || $role->state !== Abs::NORMAL) {
            return [];
        }

        $access = $this->repo(BswAdminRoleAccessControl::class)->lister(
            [
                'limit'  => 0,
                'alias'  => 'ac',
                'select' => ['ac.routeName AS route'],
                'where'  => [
                    $this->expr->eq('ac.roleId', ':role'),
                    $this->expr->eq('ac.state', ':state'),
                ],
                'args'   => [
                    'role'  => [$roleId],
                    'state' => [Abs::NORMAL],
                ],
            ]
        );

        $access = array_column($access, 'route');
        $access = Helper::arrayValuesSetTo($access, true, true);

        return $access;
    }

    /**
     * Get access of user
     *
     * @param int $userId
     *
     * @return array
     * @throws
     */
    public function getAccessOfUser(int $userId = null): array
    {
        $userId = $userId ?? $this->usr('usr_uid');
        if (empty($userId)) {
            return [];
        }

        $access = $this->repo(BswAdminAccessControl::class)->lister(
            [
                'limit'  => 0,
                'alias'  => 'ac',
                'select' => ['ac.routeName AS route'],
                'where'  => [
                    $this->expr->eq('ac.userId', ':user'),
                    $this->expr->eq('ac.state', ':state'),
                ],
                'args'   => [
                    'user'  => [$userId],
                    'state' => [Abs::NORMAL],
                ],
            ]
        );

        $access = array_column($access, 'route');
        $access = Helper::arrayValuesSetTo($access, true, true);

        return $access;
    }

    /**
     * Get access of role by user id
     *
     * @param int $userId
     *
     * @return array
     * @throws
     */
    public function getAccessOfRoleByUserId(int $userId = null): array
    {
        $userId = $userId ?? $this->usr('usr_uid');
        if (empty($userId)) {
            return [];
        }

        /**
         * @var BswAdminUserRepository $userRepo
         */
        $userRepo = $this->repo(BswAdminUser::class);
        $user = $userRepo->find($userId);

        return $this->getAccessOfRole($user->roleId);
    }

    /**
     * Get access of use with role
     *
     * @param int $userId
     *
     * @return array
     */
    public function getAccessOfUserWithRole(int $userId = null): array
    {
        $userId = $userId ?? $this->usr('usr_uid');
        if (empty($userId)) {
            return [];
        }

        $role = $this->getAccessOfRoleByUserId($userId);
        $user = $this->getAccessOfUser($userId);

        return array_merge($role, $user);
    }

    /**
     * Login admin user
     *
     * @param object $user
     * @param string $ip
     *
     * @throws
     */
    protected function loginAdminUser($user, string $ip)
    {
        /**
         * login log
         */

        $now = date(Abs::FMT_FULL);
        if ($this->parameter('backend_with_login_log')) {

            try {
                $location = $this->ip2regionIPDB($ip);
                $location = $location['location'] ?? 'Unknown';
            } catch (Exception $e) {
                $location = 'Unknown';
            }

            /**
             * @var BswAdminLoginRepository $loginLogger
             */
            $loginLogger = $this->repo(BswAdminLogin::class);
            $loginLogger->newly(
                [
                    'userId'   => $user->id,
                    'location' => Html::cleanHtml($location),
                    'ip'       => $ip,
                    'addTime'  => $now,
                ]
            );
        }

        /**
         * avatar
         */

        $avatar = null;
        if ($user->avatarAttachmentId) {

            /**
             * @var BswAttachmentRepository $avatarRepo
             */
            $avatarRepo = $this->repo(BswAttachment::class);
            $avatar = $avatarRepo->find($user->avatarAttachmentId);
            $avatar = $this->attachmentPreviewHandler($avatar, 'avatar')->avatar ?? null;
        }

        /**
         * login
         */
        $this->session->set(
            $this->skUser,
            [
                'user_id'     => $user->id,
                'phone'       => $user->phone,
                'name'        => $user->name,
                'role_id'     => $user->roleId,
                'team_id'     => $user->teamId,
                'team_leader' => $user->teamLeader,
                'sex'         => $user->sex,
                'update_time' => $user->updateTime,
                'login_time'  => $now,
                'ip'          => $ip,
                'avatar'      => $avatar,
            ]
        );
    }

    /**
     * Module header menu
     *
     * @return Menu[]
     */
    public function moduleHeaderMenu(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSkinList(): array
    {
        return [
            'terse'   => 'terse',
            'classic' => '',
        ];
    }

    /**
     * @return array
     */
    public function getThemeList(): array
    {
        return [
            'talk'    => Abs::CSS_ANT_D_TALK,
            'bsw'     => Abs::CSS_ANT_D_BSW,
            'ant-d'   => Abs::CSS_ANT_D,
            'ali-yun' => Abs::CSS_ANT_D_ALI,
        ];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getSkinByKey(string $key): array
    {
        $map = $this->getSkinList();
        if (isset($map[$key])) {
            return [$key, $map[$key]];
        }
        foreach ($map as $k => $v) {
            if (strpos($k, $key) !== false) {
                return [$k, $v];
            }
        }

        return [key($map), current($map)];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getThemeByKey(string $key): array
    {
        $map = $this->getThemeList();
        if (isset($map[$key])) {
            return [$key, $map[$key]];
        }
        foreach ($map as $k => $v) {
            if (strpos($k, $key) !== false) {
                return [$k, $v];
            }
        }

        return [key($map), current($map)];
    }

    /**
     * Module header setting
     *
     * @return Setting[]
     */
    public function moduleHeaderSetting(): array
    {
        $link = function (string $key, string $scene) {
            return (new Links())
                ->setLabel("Change {$scene} to {$key}")
                ->setIcon(['theme' => 'b:icon-apparel', 'skin' => 'b:icon-fashion-accessories'][$scene])
                ->setRoute(['theme' => $this->cnf->route_theme, 'skin' => $this->cnf->route_skin][$scene])
                ->setClick('requestByAjax')
                ->setArgs(['key' => $key]);
        };

        $skin = $this->parameter('skin', 'classic') ?: 'classic';
        $theme = $this->cnf->theme;

        return [
            $link('classic', 'skin')
                ->setChecked($skin == 'classic'),

            $link('terse', 'skin')
                ->setChecked($skin == 'terse')
                ->setAfterOriginal('<a-menu-divider></a-menu-divider>'),

            $link('ant-d', 'theme')
                ->setChecked($theme == Abs::CSS_ANT_D),

            $link('bsw', 'theme')
                ->setChecked($theme == Abs::CSS_ANT_D_BSW),

            $link('ali-yun', 'theme')
                ->setChecked($theme == Abs::CSS_ANT_D_ALI),

            $link('talk', 'theme')
                ->setChecked($theme == Abs::CSS_ANT_D_TALK)
                ->setAfterOriginal('<a-menu-divider></a-menu-divider>'),

            new Setting('Switch theme', $this->cnf->icon_theme, 'themeSwitch'),
            new Setting('Switch color weak', $this->cnf->icon_bulb, 'colorWeakSwitch'),
            new Setting('Switch third message', $this->cnf->icon_message, 'thirdMessageSwitch'),
            (new Setting())
                ->setLabel('Switch full screen')
                ->setIcon($this->cnf->icon_speech)
                ->setClick('fullScreenToggle')
                ->setArgs(['element' => 'html']),
        ];
    }

    /**
     * Module header links
     *
     * @return Links[]
     */
    public function moduleHeaderLinks(): array
    {
        if (!$this->urlSafe($this->cnf->route_clean_frontend)) {
            $this->cnf->route_clean_frontend = false;
        }

        $links = [
            new Links('Clean backend cache', $this->cnf->route_clean_backend, $this->cnf->icon_db),
            new Links('Clean project cache', $this->cnf->route_clean_project, 'b:icon-warning'),
            new Links('Profile', $this->cnf->route_profile, $this->cnf->icon_profile),
            new Links('Logout', $this->cnf->route_logout, $this->cnf->icon_logout),
        ];

        if ($this->cnf->route_clean_frontend) {
            $link = new Links('Clean frontend cache', $this->cnf->route_clean_frontend, $this->cnf->icon_redis);
            $links = Helper::arrayInsert($links, 1, [$link]);
        }

        return $links;
    }

    /**
     * Module header language
     *
     * @return array
     */
    public function moduleHeaderLanguage(): array
    {
        return [
            'cn' => '简体中文',
            'hk' => '繁體中文',
            'en' => 'English',
        ];
    }

    /**
     * Preview filter
     *
     * @param array $filter
     * @param array $index
     * @param bool  $arrayValueToString
     * @param array $handling
     *
     * @return array
     */
    public function previewFilter(
        array $filter,
        array $index = [],
        bool $arrayValueToString = true,
        array $handling = []
    ): array {

        foreach ($filter as $key => $value) {
            $k = Helper::camelToUnder($key);
            if (strpos($k, Abs::FILTER_INDEX_SPLIT) === false) {
                $k = $k . Abs::FILTER_INDEX_SPLIT . ($index[$key] ?? 0);
            }

            if (is_scalar($value)) {
                $handling[$k] = $value;
            } elseif (is_array($value)) {
                if ($arrayValueToString) {
                    $handling[$k] = implode(Abs::FORM_DATA_SPLIT, $value);
                } else {
                    foreach ($value as $index => $item) {
                        $handling[$k][$index] = $item;
                    }
                }
            }
        }

        return ['filter' => $handling];
    }

    /**
     * Html -> button
     *
     * @param Button $button
     * @param bool   $vue
     *
     * @return string
     */
    public function getButtonHtml(Button $button, bool $vue = false): string
    {
        $twig = $vue ? 'form/button.html' : 'form/button.native.html';
        if (empty($button->getUrl())) {
            $button->setUrl($this->urlSafe($button->getRoute(), $button->getArgs(), 'Build charm operates'));
        }

        return $this->renderPart($twig, ['form' => $button]);
    }

    /**
     * Database operation logger
     *
     * @param string $entity
     * @param int    $type
     * @param array  $before
     * @param array  $later
     * @param array  $effect
     *
     * @throws
     */
    public function databaseOperationLogger(
        string $entity,
        int $type,
        array $before = [],
        array $later = [],
        array $effect = []
    ) {
        if (!$this->parameter('backend_db_logger')) {
            return;
        }

        /**
         * @var BswAdminPersistenceLogRepository $loggerRepo
         */
        $loggerRepo = $this->repo(BswAdminPersistenceLog::class);
        $result = $loggerRepo->newly(
            [
                'table'  => Helper::tableNameFromCls($entity),
                'userId' => $this->usr('usr_uid') ?? 0,
                'type'   => $type,
                'before' => Helper::jsonStringify($before),
                'later'  => Helper::jsonStringify($later),
                'effect' => Helper::jsonStringify($effect),
            ]
        );

        if ($result === false) {
            $this->logger->error("Database operation logger error: {$loggerRepo->pop()}");
        }
    }

    /**
     * Upload options
     *
     * @param string $flag
     * @param array  $option
     *
     * @return array
     */
    public function uploadOptionsHandler(string $flag, array $option): array
    {
        if ($flag === 'mixed') {
            return array_merge(
                $option,
                [
                    'fileFn' => function ($file) {
                        $file->href = 'app_bsw_attachment_preview';

                        return $file;
                    },
                ]
            );
        }

        return $option;
    }
}