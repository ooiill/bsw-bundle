<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Block
{
    /**
     * @var bool
     */
    protected $block = false;

    /**
     * @return bool
     */
    public function isBlock(): bool
    {
        return $this->block;
    }

    /**
     * @param bool $block
     *
     * @return $this
     */
    public function setBlock(bool $block = true)
    {
        $this->block = $block;

        return $this;
    }
}