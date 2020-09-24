<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\Crumbs\Entity\Crumb;

trait WebCrumbs
{
    /**
     * @var array
     */
    public $crumbs = [];

    /**
     * @var array
     */
    public $correctCrumbs = [];

    /**
     * Crumbs builder
     *
     * @param string $route
     * @param array  $allMenuDetail
     *
     * @return array
     */
    public function crumbsBuilder(string $route, array $allMenuDetail = []): array
    {
        return $this->caching(
            function () use ($route, $allMenuDetail) {

                $crumbsMap = $this->parameters('crumbs_map');
                $routes = $this->getRouteCollection();
                $routeFnMap = Helper::arrayColumn($routes, 'desc_fn', 'route');

                /**
                 * In stack
                 *
                 * @param string $route
                 * @param array  $stack
                 *
                 * @return array
                 */
                $inStack = function (string $route, array $stack = []) use (
                    $crumbsMap,
                    $routeFnMap,
                    $allMenuDetail,
                    &$inStack
                ) {
                    // manual
                    if (isset($crumbsMap[$route])) {
                        $info = $allMenuDetail[$route]['info'] ?? $this->twigLang($routeFnMap[$route]);
                        array_unshift($stack, new Crumb($info, $route));

                        return $inStack($crumbsMap[$route], $stack);
                    }

                    // auto
                    if (!empty($routeFnMap[$route])) {
                        $info = $allMenuDetail[$route]['info'] ?? $this->twigLang($routeFnMap[$route]);
                        array_unshift($stack, new Crumb($info, $route));

                        foreach ($this->parameters('crumbs_preview_pre') as $keyword) {
                            if (strpos($route, "_{$keyword}") === false) {
                                continue;
                            }

                            $routeHandling = str_replace("_{$keyword}", '_preview', $route);

                            return $inStack($routeHandling, $stack);
                        }
                    }

                    return $stack;
                };

                $stack = $inStack($route);
                if (count($stack) < $this->cnf->crumbs_min_level - 2) {
                    return [];
                }

                $crumbsIconMap = $this->parameters('crumbs_keyword_to_icon_map');
                array_unshift($stack, new Crumb('Home', $this->cnf->route_default, $crumbsIconMap['home']));

                /**
                 * @var Crumb[] $stack
                 */
                $count = count($stack);
                foreach ($stack as $i => $item) {
                    if ($i == $count - 1) {
                        $item->setRoute(null);
                    }
                    foreach ($crumbsIconMap as $keyword => $icon) {
                        if (strpos($item->getRoute(), "_{$keyword}") !== false) {
                            $item->setIcon($icon);
                            break;
                        }
                    }
                }

                return $stack;
            }
        );
    }

    /**
     * Change crumbs
     *
     * @param string $title
     * @param string $icon
     * @param string $route
     * @param int    $index
     *
     * @return int
     */
    public function changeCrumbs(
        ?string $title = null,
        ?string $icon = null,
        ?string $route = null,
        ?int $index = null
    ): int {
        return array_push(
            $this->correctCrumbs,
            [
                'mode'  => 'change',
                'title' => $title,
                'icon'  => $icon,
                'route' => $route,
                'index' => $index,
            ]
        );
    }

    /**
     * Append crumbs
     *
     * @param string $title
     * @param string $icon
     * @param string $route
     *
     * @return int
     */
    public function appendCrumbs(string $title, ?string $icon = null, ?string $route = null): int
    {
        return array_push(
            $this->correctCrumbs,
            [
                'mode'  => 'append',
                'title' => $title,
                'icon'  => $icon,
                'route' => $route,
            ]
        );
    }

    /**
     * Remove crumbs
     *
     * @param int $index
     *
     * @return int
     */
    public function removeCrumbs(?int $index = null): int
    {
        return array_push(
            $this->correctCrumbs,
            [
                'mode'  => 'remove',
                'index' => $index,
            ]
        );
    }

    /**
     * Correct crumbs
     */
    public function correctCrumbs()
    {
        if (empty($this->crumbs)) {
            return;
        }

        $total = count($this->crumbs);
        foreach ($this->correctCrumbs as $key => $item) {

            /**
             * @var string $mode
             * @var string $title
             * @var string $icon
             * @var string $route
             * @var int    $index
             */
            extract($item);

            if ($mode == 'append') {
                array_push($this->crumbs, new Crumb($title, $route, $icon));
                continue;
            }

            $index = $index ?? $total - 1;
            $index = $index < 0 ? $total + $index : $index;
            if (!isset($this->crumbs[$index])) {
                return;
            }

            if ($mode == 'remove') {
                unset($this->crumbs[$key]);
                continue;
            }

            /**
             * @var Crumb $crumb
             */
            $crumb = $this->crumbs[$index];

            if (strpos($title, '%s') !== false) {
                $title = sprintf($title, $crumb->getLabel());
            }

            if (isset($title)) {
                $crumb->setLabel($title);
            }

            if (isset($icon)) {
                $crumb->setIcon($icon);
            }

            if (isset($route)) {
                $crumb->setRoute($route);
            }
        }

        $this->crumbs = array_values($this->crumbs);
    }
}