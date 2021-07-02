<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Responsive
{
    /**
     * @var bool
     */
    protected $responsive = true;

    /**
     * @return bool
     */
    public function isResponsive(): bool
    {
        return $this->responsive;
    }

    /**
     * @param bool $responsive
     *
     * @return $this
     */
    public function setResponsive(bool $responsive = true)
    {
        $this->responsive = $responsive;

        return $this;
    }
}