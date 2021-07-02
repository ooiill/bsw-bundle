<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SecondStep
{
    /**
     * @var int
     */
    protected $secondStep = 1;

    /**
     * @return int
     */
    public function getSecondStep(): int
    {
        return $this->secondStep;
    }

    /**
     * @param int $secondStep
     *
     * @return $this
     */
    public function setSecondStep(int $secondStep)
    {
        $this->secondStep = $secondStep;

        return $this;
    }
}