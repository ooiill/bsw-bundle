<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Label
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }
}