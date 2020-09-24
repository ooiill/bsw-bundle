<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait VarNameForTips
{
    /**
     * @var string
     */
    protected $varNameForTips;

    /**
     * @return string
     */
    public function getVarNameForTips(): string
    {
        return $this->varNameForTips;
    }

    /**
     * @param string $varNameForTips
     *
     * @return $this
     */
    public function setVarNameForTips(string $varNameForTips)
    {
        $this->varNameForTips = $varNameForTips;

        return $this;
    }
}