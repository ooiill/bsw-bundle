<?php

namespace Leon\BswBundle\Module\GetSetter;

trait NeedId
{
    /**
     * @var bool
     */
    protected $needId = true;

    /**
     * @return bool
     */
    public function isNeedId(): bool
    {
        return $this->needId;
    }

    /**
     * @param bool $needId
     *
     * @return $this
     */
    public function setNeedId(bool $needId = true)
    {
        $this->needId = $needId;

        return $this;
    }
}