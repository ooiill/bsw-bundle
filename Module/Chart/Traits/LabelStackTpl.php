<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait LabelStackTpl
{
    /**
     * @var string
     */
    protected $labelStackTpl = "{name|{a}} {c}";

    /**
     * @return string
     */
    public function getLabelStackTpl(): string
    {
        return $this->labelStackTpl;
    }

    /**
     * @param string $labelStackTpl
     *
     * @return $this
     */
    public function setLabelStackTpl(string $labelStackTpl)
    {
        $this->labelStackTpl = $labelStackTpl;

        return $this;
    }
}