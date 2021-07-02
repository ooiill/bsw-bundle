<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Step
{
    /**
     * @var float
     */
    protected $step = 1.0;

    /**
     * @return float
     */
    public function getStep(): float
    {
        return $this->step;
    }

    /**
     * @param float $step
     */
    public function setStep(float $step): void
    {
        $this->step = $step;
    }
}