<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Color
{
    /**
     * @var array
     */
    protected $color = [];

    /**
     * @return array
     */
    public function getColor(): array
    {
        return $this->color;
    }

    /**
     * @param array $color
     *
     * @return $this
     */
    public function setColor(array $color)
    {
        $this->color = $color;

        return $this;
    }
}