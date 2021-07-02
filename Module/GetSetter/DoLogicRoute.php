<?php

namespace Leon\BswBundle\Module\GetSetter;

trait DoLogicRoute
{
    /**
     * @var string
     */
    protected $doLogicRoute;

    /**
     * @var array
     */
    protected $doLogicRouteArgs = [];

    /**
     * @return string
     */
    public function getDoLogicRoute(): ?string
    {
        return $this->doLogicRoute;
    }

    /**
     * @param string $doLogicRoute
     *
     * @return $this
     */
    public function setDoLogicRoute(string $doLogicRoute)
    {
        $this->doLogicRoute = $doLogicRoute;

        return $this;
    }

    /**
     * @return array
     */
    public function getDoLogicRouteArgs(): array
    {
        return $this->doLogicRouteArgs;
    }

    /**
     * @param array $doLogicRouteArgs
     *
     * @return $this
     */
    public function setDoLogicRouteArgs(array $doLogicRouteArgs)
    {
        $this->doLogicRouteArgs = $doLogicRouteArgs;

        return $this;
    }
}