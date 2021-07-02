<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxZoom
{
    /**
     * @var int
     */
    protected $maxZoom = 50;

    /**
     * @return int
     */
    public function getMaxZoom(): int
    {
        return $this->maxZoom;
    }

    /**
     * @param int $maxZoom
     *
     * @return $this
     */
    public function setMaxZoom(int $maxZoom)
    {
        $this->maxZoom = $maxZoom;

        return $this;
    }

}