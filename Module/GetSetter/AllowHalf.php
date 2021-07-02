<?php

namespace Leon\BswBundle\Module\GetSetter;

trait AllowHalf
{
    /**
     * @var bool
     */
    protected $allowHalf = false;

    /**
     * @return bool
     */
    public function isAllowHalf(): bool
    {
        return $this->allowHalf;
    }

    /**
     * @param bool $allowHalf
     *
     * @return $this
     */
    public function setAllowHalf(bool $allowHalf = true)
    {
        $this->allowHalf = $allowHalf;

        return $this;
    }
}