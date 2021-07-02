<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Range
{
    /**
     * @var bool
     */
    protected $range = false;

    /**
     * @return bool
     */
    public function isRange(): bool
    {
        return $this->range;
    }

    /**
     * @param bool $range
     *
     * @return $this
     */
    public function setRange(bool $range = true)
    {
        $this->range = $range;

        return $this;
    }
}