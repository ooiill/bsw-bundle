<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait TooltipTpl
{
    /**
     * @var string
     */
    protected $tooltipTpl;

    /**
     * @return string
     */
    public function getTooltipTpl(): ?string
    {
        return $this->tooltipTpl;
    }

    /**
     * @param string $tooltipTpl
     *
     * @return $this
     */
    public function setTooltipTpl(string $tooltipTpl)
    {
        $this->tooltipTpl = $tooltipTpl;

        return $this;
    }
}