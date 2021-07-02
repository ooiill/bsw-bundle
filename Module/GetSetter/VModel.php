<?php

namespace Leon\BswBundle\Module\GetSetter;

trait VModel
{
    /**
     * @var string
     */
    protected $vModel = null;

    /**
     * @return string
     */
    public function getVModel(): ?string
    {
        return $this->vModel;
    }

    /**
     * @param string $vModel
     *
     * @return $this
     */
    public function setVModel(?string $vModel)
    {
        $this->vModel = $vModel;

        return $this;
    }
}