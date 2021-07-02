<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MinuteStep
{
    /**
     * @var int
     */
    protected $minuteStep = 1;

    /**
     * @return int
     */
    public function getMinuteStep(): int
    {
        return $this->minuteStep;
    }

    /**
     * @param int $minuteStep
     *
     * @return $this
     */
    public function setMinuteStep(int $minuteStep)
    {
        $this->minuteStep = $minuteStep;

        return $this;
    }
}