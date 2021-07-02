<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Confirm
{
    /**
     * @var string
     */
    protected $confirm;

    /**
     * @return string
     */
    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    /**
     * @param string $confirm
     *
     * @return $this
     */
    public function setConfirm(?string $confirm = null)
    {
        $this->confirm = $confirm;

        return $this;
    }
}