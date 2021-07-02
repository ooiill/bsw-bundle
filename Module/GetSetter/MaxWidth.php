<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxWidth
{
    /**
     * @var int
     */
    protected $maxWidth = 300;

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @param int $maxWidth
     *
     * @return $this
     */
    public function setMaxWidth(int $maxWidth)
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }
}