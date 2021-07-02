<?php

namespace Leon\BswBundle\Module\GetSetter;

trait BindLoading
{
    /**
     * @var string
     */
    protected $bindLoading;

    /**
     * @return string|null
     */
    public function getBindLoading(): ?string
    {
        return $this->bindLoading;
    }

    /**
     * @param string $bindLoading
     *
     * @return $this
     */
    public function setBindLoading(string $bindLoading)
    {
        $this->bindLoading = $bindLoading;

        return $this;
    }
}