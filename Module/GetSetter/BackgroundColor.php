<?php

namespace Leon\BswBundle\Module\GetSetter;

trait BackgroundColor
{
    /**
     * @var string
     */
    protected $backgroundColor = 'white';

    /**
     * @return string
     */
    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    /**
     * @param string $backgroundColor
     *
     * @return $this
     */
    public function setBackgroundColor(string $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }
}