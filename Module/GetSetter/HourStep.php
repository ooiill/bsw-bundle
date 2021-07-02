<?php

namespace Leon\BswBundle\Module\GetSetter;

trait HourStep
{
    /**
     * @var int
     */
    protected $hourStep = 1;

    /**
     * @return int
     */
    public function getHourStep(): int
    {
        return $this->hourStep;
    }

    /**
     * @param int $hourStep
     *
     * @return $this
     */
    public function setHourStep(int $hourStep)
    {
        $this->hourStep = $hourStep;

        return $this;
    }
}