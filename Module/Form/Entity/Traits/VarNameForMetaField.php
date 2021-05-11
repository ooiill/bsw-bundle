<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait VarNameForMetaField
{
    /**
     * @var string
     */
    protected $varNameForMetaField = null;

    /**
     * @return string
     */
    public function getVarNameForMetaField(): ?string
    {
        return $this->varNameForMetaField;
    }

    /**
     * @param string $varNameForMetaField
     *
     * @return $this
     */
    public function setVarNameForMetaField(?string $varNameForMetaField)
    {
        $this->varNameForMetaField = $varNameForMetaField;

        return $this;
    }
}