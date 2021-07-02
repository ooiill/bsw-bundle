<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MapColor
{
    /**
     * @var array
     */
    protected $mapColor = ['rgba(24, 114, 255, .1)', 'rgba(24, 114, 255, .7)'];

    /**
     * @return array
     */
    public function getMapColor(): array
    {
        return $this->mapColor;
    }

    /**
     * @param array $mapColor
     *
     * @return $this
     */
    public function setMapColor(array $mapColor)
    {
        $this->mapColor = $mapColor;

        return $this;
    }
}