<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

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