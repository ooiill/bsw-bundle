<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ButtonLabel
{
    /**
     * @var string
     */
    protected $buttonLabel = 'Click it';

    /**
     * @return string
     */
    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    /**
     * @param string $buttonLabel
     *
     * @return $this
     */
    public function setButtonLabel(string $buttonLabel)
    {
        $this->buttonLabel = $buttonLabel;

        return $this;
    }
}