<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ConfirmCheckbox
{
    /**
     * @var string
     */
    protected $confirmCheckbox;

    /**
     * @return string
     */
    public function getConfirmCheckbox(): ?string
    {
        return $this->confirmCheckbox;
    }

    /**
     * @param string $confirmCheckbox
     *
     * @return $this
     */
    public function setConfirmCheckbox(?string $confirmCheckbox = null)
    {
        $this->confirmCheckbox = $confirmCheckbox;

        return $this;
    }
}