<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxBarFixedWidth
{
    /**
     * @var int
     */
    protected $maxBarFixedWidth = 10;

    /**
     * @return int
     */
    public function getMaxBarFixedWidth(): int
    {
        return $this->maxBarFixedWidth;
    }

    /**
     * @param int $maxBarFixedWidth
     *
     * @return $this
     */
    public function setMaxBarFixedWidth(int $maxBarFixedWidth)
    {
        $this->maxBarFixedWidth = $maxBarFixedWidth;

        return $this;
    }
}