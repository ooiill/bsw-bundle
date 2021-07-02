<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ButtonBlock
{
    /**
     * @var bool
     */
    protected $buttonBlock = true;

    /**
     * @return bool
     */
    public function isButtonBlock(): bool
    {
        return $this->buttonBlock;
    }

    /**
     * @param bool $buttonBlock
     *
     * @return $this
     */
    public function setButtonBlock(bool $buttonBlock = true)
    {
        $this->buttonBlock = $buttonBlock;

        return $this;
    }
}