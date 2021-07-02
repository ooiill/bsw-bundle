<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ClassCss
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @return string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }
}