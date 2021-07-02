<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TargetOperate
{
    /**
     * @var string
     */
    protected $targetOperate = '';

    /**
     * @return string
     */
    public function getTargetOperate(): string
    {
        return $this->targetOperate;
    }

    /**
     * @param string $targetOperate
     *
     * @return $this
     */
    public function setTargetOperate(string $targetOperate)
    {
        $this->targetOperate = $targetOperate;

        return $this;
    }
}