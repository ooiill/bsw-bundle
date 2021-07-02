<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TooltipVisible
{
    /**
     * @var bool|null
     */
    protected $tooltipVisible = null;

    /**
     * @return bool
     */
    public function isTooltipVisible(): ?bool
    {
        return $this->tooltipVisible;
    }

    /**
     * @param bool $tooltipVisible
     *
     * @return $this
     */
    public function setTooltipVisible(bool $tooltipVisible = true)
    {
        $this->tooltipVisible = $tooltipVisible;

        return $this;
    }
}