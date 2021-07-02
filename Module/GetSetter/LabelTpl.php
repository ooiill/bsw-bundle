<?php

namespace Leon\BswBundle\Module\GetSetter;

trait LabelTpl
{
    /**
     * @var string
     */
    protected $labelTpl = "{text|{b}}\n{hr|}\n{text|{c} ({d}%)}";

    /**
     * @return string
     */
    public function getLabelTpl(): string
    {
        return $this->labelTpl;
    }

    /**
     * @param string $labelTpl
     *
     * @return $this
     */
    public function setLabelTpl(string $labelTpl)
    {
        $this->labelTpl = $labelTpl;

        return $this;
    }
}