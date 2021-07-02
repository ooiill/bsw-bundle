<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxValue
{
    /**
     * @var integer
     */
    protected $maxValue = PHP_INT_MIN;

    /**
     * @return int
     */
    public function getMaxValue(): int
    {
        return $this->maxValue;
    }

    /**
     * @param int $maxValue
     *
     * @return $this
     */
    public function setMaxValue(int $maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }
}