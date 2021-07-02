<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ValueTpl
{
    /**
     * @var string
     */
    protected $valueTpl = null;

    /**
     * @return string
     */
    public function getValueTpl(): ?string
    {
        return $this->valueTpl;
    }

    /**
     * @param string $valueTpl
     *
     * @return $this
     */
    public function setValueTpl(string $valueTpl)
    {
        $this->valueTpl = $valueTpl;

        return $this;
    }
}