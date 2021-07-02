<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Blur
{
    /**
     * @var string
     */
    protected $blur;

    /**
     * @return string
     */
    public function getBlur(): ?string
    {
        return $this->blur;
    }

    /**
     * @param string $blur
     *
     * @return $this
     */
    public function setBlur(string $blur = null)
    {
        $this->blur = $blur;

        return $this;
    }
}