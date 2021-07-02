<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Mobile
{
    /**
     * @var bool
     */
    protected $mobile = false;

    /**
     * @return bool
     */
    public function isMobile(): bool
    {
        return $this->mobile;
    }

    /**
     * @param bool $mobile
     *
     * @return $this
     */
    public function setMobile(bool $mobile = true)
    {
        $this->mobile = $mobile;

        return $this;
    }
}