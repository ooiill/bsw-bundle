<?php

namespace Leon\BswBundle\Module\GetSetter;

trait BackFill
{
    /**
     * @var bool
     */
    protected $backFill = true;

    /**
     * @return bool
     */
    public function isBackFill(): bool
    {
        return $this->backFill;
    }

    /**
     * @param bool $backFill
     *
     * @return $this
     */
    public function setBackFill(bool $backFill = true)
    {
        $this->backFill = $backFill;

        return $this;
    }
}