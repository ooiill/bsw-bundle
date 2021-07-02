<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Placement
{
    /**
     * @var string
     */
    protected $placement;

    /**
     * @return string
     */
    public function getPlacement(): ?string
    {
        return $this->placement;
    }

    /**
     * @param string $placement
     *
     * @return $this
     */
    public function setPlacement(string $placement)
    {
        $this->placement = $placement;

        return $this;
    }
}