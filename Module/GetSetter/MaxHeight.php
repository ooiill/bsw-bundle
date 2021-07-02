<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxHeight
{
    /**
     * @var int
     */
    protected $maxHeight = 300;

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @param int $maxHeight
     *
     * @return $this
     */
    public function setMaxHeight(int $maxHeight)
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }
}