<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ShowLabel
{
    /**
     * @var bool
     */
    protected $showLabel = true;

    /**
     * @return bool
     */
    public function isShowLabel(): bool
    {
        return $this->showLabel;
    }

    /**
     * @param bool $showLabel
     *
     * @return $this
     */
    public function setShowLabel(bool $showLabel = true)
    {
        $this->showLabel = $showLabel;

        return $this;
    }
}