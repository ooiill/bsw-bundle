<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ComplexKey
{
    /**
     * @var bool
     */
    protected $complexKey = true;

    /**
     * @return bool
     */
    public function isComplexKey(): bool
    {
        return $this->complexKey;
    }

    /**
     * @param bool $complexKey
     *
     * @return $this
     */
    public function setComplexKey(bool $complexKey = false)
    {
        $this->complexKey = $complexKey;

        return $this;
    }
}