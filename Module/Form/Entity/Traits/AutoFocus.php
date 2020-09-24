<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait AutoFocus
{
    /**
     * @var bool
     */
    protected $autoFocus = false;

    /**
     * @return bool
     */
    public function isAutoFocus(): bool
    {
        return $this->autoFocus;
    }

    /**
     * @param bool $autoFocus
     *
     * @return $this
     */
    public function setAutoFocus(bool $autoFocus = true)
    {
        $this->autoFocus = $autoFocus;

        return $this;
    }
}