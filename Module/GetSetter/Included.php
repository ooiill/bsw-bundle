<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Included
{
    /**
     * @var bool
     */
    protected $included = true;

    /**
     * @return bool
     */
    public function isIncluded(): bool
    {
        return $this->included;
    }

    /**
     * @param bool $included
     *
     * @return $this
     */
    public function setIncluded(bool $included = true)
    {
        $this->included = $included;

        return $this;
    }
}