<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Vertical
{
    /**
     * @var bool
     */
    protected $vertical = false;

    /**
     * @return bool
     */
    public function isVertical(): bool
    {
        return $this->vertical;
    }

    /**
     * @param bool $vertical
     *
     * @return $this
     */
    public function setVertical(bool $vertical = true)
    {
        $this->vertical = $vertical;

        return $this;
    }
}