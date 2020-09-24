<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Placeholder
{
    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @return string
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     *
     * @return $this
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}