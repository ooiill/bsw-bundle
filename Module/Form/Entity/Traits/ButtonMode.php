<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ButtonMode
{
    /**
     * @var bool
     */
    protected $buttonMode = false;

    /**
     * @return bool
     */
    public function isButtonMode(): bool
    {
        return $this->buttonMode;
    }

    /**
     * @param bool $buttonMode
     *
     * @return $this
     */
    public function setButtonMode(bool $buttonMode = true)
    {
        $this->buttonMode = $buttonMode;

        return $this;
    }
}