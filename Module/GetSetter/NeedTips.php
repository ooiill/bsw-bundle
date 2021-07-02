<?php

namespace Leon\BswBundle\Module\GetSetter;

trait NeedTips
{
    /**
     * @var bool
     */
    protected $needTips = true;

    /**
     * @return bool
     */
    public function isNeedTips(): bool
    {
        return $this->needTips;
    }

    /**
     * @param bool $needTips
     *
     * @return $this
     */
    public function setNeedTips(bool $needTips = true)
    {
        $this->needTips = $needTips;

        return $this;
    }
}