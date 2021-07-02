<?php

namespace Leon\BswBundle\Module\GetSetter;

trait LabelInValue
{
    /**
     * @var bool
     */
    protected $labelInValue = false;

    /**
     * @return bool
     */
    public function isLabelInValue(): bool
    {
        return $this->labelInValue;
    }

    /**
     * @param bool $labelInValue
     *
     * @return $this
     */
    public function setLabelInValue(bool $labelInValue = true)
    {
        $this->labelInValue = $labelInValue;

        return $this;
    }
}