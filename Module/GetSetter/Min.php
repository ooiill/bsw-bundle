<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Min
{
    /**
     * @var float|int
     */
    protected $min = 0;

    /**
     * @return float|int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param $min
     *
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }
}