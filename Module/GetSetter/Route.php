<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Route
{
    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeForAccess = [];

    /**
     * @param string $route
     *
     * @return string
     */
    public function getRoute(string $route = ''): string
    {
        return trim($this->route ?? $route);
    }

    /**
     * @param string $route
     *
     * @return $this
     */
    public function setRoute(?string $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return array
     */
    public function getRouteForAccess(): array
    {
        return $this->routeForAccess ?: [$this->route];
    }

    /**
     * @param array $routeForAccess
     *
     * @return $this
     */
    public function setRouteForAccess(array $routeForAccess)
    {
        $this->routeForAccess = $routeForAccess;

        return $this;
    }

    /**
     * @param string $routeForAccess
     *
     * @return $this
     */
    public function pushRouteForAccess(string $routeForAccess)
    {
        array_push($this->routeForAccess, $routeForAccess);

        return $this;
    }
}
