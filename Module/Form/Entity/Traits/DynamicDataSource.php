<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait DynamicDataSource
{
    /**
     * @var string
     */
    protected $ddsRoute;

    /**
     * @var array
     */
    protected $ddsArgs = [];

    /**
     * @return string
     */
    public function getDdsRoute(): ?string
    {
        return $this->ddsRoute;
    }

    /**
     * @param string $ddsRoute
     *
     * @return $this
     */
    public function setDdsRoute(string $ddsRoute)
    {
        $this->ddsRoute = $ddsRoute;

        return $this;
    }

    /**
     * @return array
     */
    public function getDdsArgs(): array
    {
        return $this->ddsArgs;
    }

    /**
     * @param array $ddsArgs
     *
     * @return $this
     */
    public function setDdsArgs(array $ddsArgs)
    {
        $this->ddsArgs = $ddsArgs;

        return $this;
    }
}