<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SourceOperate
{
    /**
     * @var string
     */
    protected $sourceOperate = '';

    /**
     * @return string
     */
    public function getSourceOperate(): string
    {
        return $this->sourceOperate;
    }

    /**
     * @param string $sourceOperate
     *
     * @return $this
     */
    public function setSourceOperate(string $sourceOperate)
    {
        $this->sourceOperate = $sourceOperate;

        return $this;
    }
}