<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait VarNameForSelector
{
    /**
     * @var string
     */
    protected $varNameForSelector;

    /**
     * @return string
     */
    public function getVarNameForSelector(): string
    {
        return $this->varNameForSelector;
    }

    /**
     * @param string $varNameForSelector
     *
     * @return $this
     */
    public function setVarNameForSelector(string $varNameForSelector)
    {
        $this->varNameForSelector = $varNameForSelector;

        return $this;
    }
}