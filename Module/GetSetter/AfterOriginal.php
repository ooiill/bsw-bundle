<?php

namespace Leon\BswBundle\Module\GetSetter;

trait AfterOriginal
{
    /**
     * @var string
     */
    public $afterOriginal;

    /**
     * @return string
     */
    public function getAfterOriginal(): ?string
    {
        return $this->afterOriginal;
    }

    /**
     * @param string $afterOriginal
     *
     * @return $this
     */
    public function setAfterOriginal(string $afterOriginal)
    {
        $this->afterOriginal = $afterOriginal;

        return $this;
    }
}