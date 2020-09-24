<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

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