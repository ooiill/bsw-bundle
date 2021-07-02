<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MinWidth
{
    /**
     * @var int
     */
    protected $minWidth = 100;

    /**
     * @return int
     */
    public function getMinWidth(): int
    {
        return $this->minWidth;
    }

    /**
     * @param int $minWidth
     *
     * @return $this
     */
    public function setMinWidth(int $minWidth)
    {
        $this->minWidth = $minWidth;

        return $this;
    }
}