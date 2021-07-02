<?php

namespace Leon\BswBundle\Module\GetSetter;

trait BeforeOriginal
{
    /**
     * @var string
     */
    protected $beforeOriginal;

    /**
     * @return string
     */
    public function getBeforeOriginal(): ?string
    {
        return $this->beforeOriginal;
    }

    /**
     * @param string $beforeOriginal
     *
     * @return $this
     */
    public function setBeforeOriginal(string $beforeOriginal)
    {
        $this->beforeOriginal = $beforeOriginal;

        return $this;
    }
}