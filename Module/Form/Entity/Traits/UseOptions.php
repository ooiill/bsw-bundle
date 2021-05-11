<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait UseOptions
{
    /**
     * @var bool
     */
    protected $useOptions = false;

    /**
     * @return bool
     */
    public function isUseOptions(): bool
    {
        return $this->useOptions;
    }

    /**
     * @param bool $useOptions
     *
     * @return $this
     */
    public function setUseOptions(bool $useOptions = true)
    {
        $this->useOptions = $useOptions;

        return $this;
    }
}