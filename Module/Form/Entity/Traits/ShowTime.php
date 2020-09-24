<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ShowTime
{
    /**
     * @var bool
     */
    protected $showTime = true;

    /**
     * @return bool
     */
    public function isShowTime(): bool
    {
        return $this->showTime;
    }

    /**
     * @param bool $showTime
     *
     * @return $this
     */
    public function setShowTime(bool $showTime)
    {
        $this->showTime = $showTime;

        return $this;
    }
}