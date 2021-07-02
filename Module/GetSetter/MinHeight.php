<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MinHeight
{
    /**
     * @var int
     */
    protected $minHeight = 100;

    /**
     * @return int
     */
    public function getMinHeight(): int
    {
        return $this->minHeight;
    }

    /**
     * @param int $minHeight
     *
     * @return $this
     */
    public function setMinHeight(int $minHeight)
    {
        $this->minHeight = $minHeight;

        return $this;
    }
}