<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ShowArrow
{
    /**
     * @var bool
     */
    protected $showArrow = true;

    /**
     * @return bool
     */
    public function isShowArrow(): bool
    {
        return $this->showArrow;
    }

    /**
     * @param bool $showArrow
     *
     * @return $this
     */
    public function setShowArrow(bool $showArrow = true)
    {
        $this->showArrow = $showArrow;

        return $this;
    }
}