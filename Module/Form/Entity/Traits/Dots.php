<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Dots
{
    /**
     * @var string
     */
    protected $dots = false;

    /**
     * @return bool
     */
    public function isDots(): bool
    {
        return $this->dots;
    }

    /**
     * @param bool $dots
     *
     * @return $this
     */
    public function setDots(bool $dots = true)
    {
        $this->dots = $dots;

        return $this;
    }
}