<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TooltipPosition
{
    /**
     * @var string
     */
    protected $tooltipPosition;

    /**
     * @return string
     */
    public function getTooltipPosition(): ?string
    {
        return $this->tooltipPosition;
    }

    /**
     * @param string $tooltipPosition
     *
     * @return $this
     */
    public function setTooltipPosition(string $tooltipPosition)
    {
        $this->tooltipPosition = $tooltipPosition;

        return $this;
    }
}