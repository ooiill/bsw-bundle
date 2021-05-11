<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait VarNameForChange
{
    /**
     * @var string
     */
    protected $varNameForChange = null;

    /**
     * @return string
     */
    public function getVarNameForChange(): ?string
    {
        return $this->varNameForChange;
    }

    /**
     * @param string $varNameForChange
     *
     * @return $this
     */
    public function setVarNameForChange(?string $varNameForChange)
    {
        $this->varNameForChange = $varNameForChange;

        return $this;
    }
}