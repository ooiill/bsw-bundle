<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MinValue
{
    /**
     * @var integer
     */
    protected $minValue = PHP_INT_MAX;

    /**
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->minValue;
    }

    /**
     * @param int $minValue
     *
     * @return $this
     */
    public function setMinValue(int $minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }
}