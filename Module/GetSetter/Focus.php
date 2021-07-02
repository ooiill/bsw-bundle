<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Focus
{
    /**
     * @var string
     */
    protected $focus;

    /**
     * @return string
     */
    public function getFocus(): ?string
    {
        return $this->focus;
    }

    /**
     * @param string $focus
     *
     * @return $this
     */
    public function setFocus(string $focus = null)
    {
        $this->focus = $focus;

        return $this;
    }
}