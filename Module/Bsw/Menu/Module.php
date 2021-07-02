<?php

namespace Leon\BswBundle\Module\Bsw\Menu;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Scene\Menu;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'menu';
    }

    /**
     * @return array
     */
    public function javascript(): ?array
    {
        return ['module-scaffold' => 'diy;module/scaffold.js'];
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * @return array
     */
    protected function listMenu(): array
    {
        return $this->web->caching(
            function () {
                $filter = [
                    'limit'  => 0,
                    'select' => [
                        'bam.id',
                        'bam.menuId',
                        'bam.routeName AS route',
                        'bam.icon',
                        'bam.value AS label',
                        'bam.javascript AS click',
                        'bam.jsonParams AS args',
                    ],
                    'where'  => [$this->input->expr->eq('bam.state', ':state')],
                    'args'   => ['state' => [Abs::NORMAL]],
                    'sort'   => ['bam.sort' => Abs::SORT_ASC],
                ];

                $list = $this->web->repo(BswAdminMenu::class)->lister($filter);
                $menuList = [];

                foreach ($list as $item) {
                    $item['args'] = Helper::parseJsonString($item['args'], []);
                    $item['label'] = $this->web->twigLang($item['label']);
                    array_push($menuList, (new Menu())->attributes($item));
                }

                return $menuList;
            },
            'app_admin_menu'
        );
    }

    /**
     * @param bool $forceFull
     *
     * @return array
     */
    protected function menuBuilder(bool $forceFull = false): array
    {
        $menu = $masterMenuDetail = $slaveMenuDetail = [];
        $parent = $current = $masterIndex = 0;

        $menuList = $this->listMenu();
        if (empty($menuList)) {
            return [
                $menu,
                $menu,
                $masterMenuDetail,
                $slaveMenuDetail,
                $parent,
                $current,
            ];
        }

        // current and parent
        $currentMap = $this->web->parameters('menus_same_current_map');
        $parentMap = $this->web->parameters('menus_same_parent_map');

        foreach ($menuList as $item) {

            /**
             * @var Menu $item
             */
            $route = $item->getRoute();
            if (!$this->web->routeIsAccess([$route])) {
                continue;
            }

            $args = $item->getArgs(true);

            // route path
            if ($route) {
                $item->setUrl($this->web->urlSafe($route, $args, 'Build admin menu'));
            }

            // javascript
            if ($click = $item->getClick()) {
                foreach ($args as $k => $v) {
                    $args[$k] = str_replace('{ROUTE}', $item->getUrl(), $v);
                    if ($k == 'title') {
                        $args[$k] = $this->web->twigLang($v);
                    }
                }
                $args = Helper::numericValues($args);
                $item->setArgs(array_merge(['function' => $click], $args));
            }

            $menu[$item->getMenuId()][$item->getId()] = $item;
            if ($item->getMenuId() !== $masterIndex) {
                $slaveMenuDetail[$item->getRoute()] = [
                    'info'          => $item->getLabel(),
                    'parentMenuId'  => $item->getMenuId(),
                    'currentMenuId' => $item->getId(),
                ];
            }

            $currentRoute = $this->input->route;
            if (isset($currentMap[$currentRoute])) {
                $currentRoute = $currentMap[$currentRoute];
            } elseif (isset($parentMap[$currentRoute])) {
                $sameParentOnly = true;
                $currentRoute = $parentMap[$currentRoute];
            }

            foreach ($this->web->parameters('crumbs_preview_pre') as $keyword) {
                $currentRoute = preg_replace("/_{$keyword}$/i", '_preview', $currentRoute);
            }

            if ($route == $currentRoute) {
                $parent = $item->getMenuId() ?: $masterIndex;
                $current = empty($sameParentOnly) ? $item->getId() : 0;
            }
        }

        $masterMenuRough = Helper::dig($menu, $masterIndex);
        $slaveMenu = $menu;

        $masterMenu = $masterMenuDetail = [];
        foreach ($masterMenuRough as $index => $item) {
            if (!empty($slaveMenu[$index]) || !empty($item->getRoute())) {
                $masterMenu[$index] = $item;
                if (!empty($item->getRoute())) {
                    $masterMenuDetail[$item->getRoute()] = [
                        'info'          => $item->getLabel(),
                        'parentMenuId'  => $item->getMenuId(),
                        'currentMenuId' => $item->getId(),
                    ];
                }
            }
        }

        // correct parent
        if (empty($parent)) {
            $parent = $slaveMenuDetail[$this->input->route]['parentMenuId'] ?? 0;
        }

        // correct current
        if (empty($current)) {
            $current = $slaveMenuDetail[$this->input->route]['currentMenuId'] ?? 0;
        }

        return [
            $masterMenu,
            $slaveMenu,
            $masterMenuDetail,
            $slaveMenuDetail,
            $parent,
            $current,
        ];
    }

    /**
     * @return ArgsOutput
     */
    public function logic(): ArgsOutput
    {
        $output = new Output($this->input);

        [
            $output->masterMenu,
            $output->slaveMenu,
            $output->masterMenuDetail,
            $output->slaveMenuDetail,
            $output->parent,
            $output->current,
        ] = $this->menuBuilder();

        if (strpos($this->input->route, 'grant') !== false) {
            [
                $output->masterMenuForRender,
                $output->slaveMenuForRender,
                $output->masterMenuDetailForRender,
                $output->slaveMenuDetailForRender,
            ] = $this->menuBuilder(true);
        }

        $output = $this->caller(
            $this->method(),
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'), $this->input->args)
        );

        return $output;
    }
}