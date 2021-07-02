<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Ghost
{
    /**
     * @var bool
     */
    protected $ghost = false;

    /**
     * @return bool
     */
    public function isGhost(): bool
    {
        return $this->ghost;
    }

    /**
     * @param bool $ghost
     *
     * @return $this
     */
    public function setGhost(bool $ghost = true)
    {
        $this->ghost = $ghost;

        return $this;
    }
}