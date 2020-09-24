<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Click
{
    /**
     * @var string
     */
    protected $click = 'redirect';

    /**
     * @return string
     */
    public function getClick(): string
    {
        return $this->click;
    }

    /**
     * @param string $click
     *
     * @return $this
     */
    public function setClick(string $click)
    {
        $this->click = $click;

        return $this;
    }
}