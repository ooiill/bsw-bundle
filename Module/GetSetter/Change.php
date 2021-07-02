<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Change
{
    /**
     * @var string
     */
    protected $change;

    /**
     * @return string
     */
    public function getChange(): ?string
    {
        return $this->change;
    }

    /**
     * @param string $change
     *
     * @return $this
     */
    public function setChange(string $change = null)
    {
        $this->change = $change;

        return $this;
    }
}