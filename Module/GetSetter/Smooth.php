<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Smooth
{
    /**
     * @var bool
     */
    protected $smooth = true;

    /**
     * @return bool
     */
    public function isSmooth(): bool
    {
        return $this->smooth;
    }

    /**
     * @param bool $smooth
     *
     * @return $this
     */
    public function setSmooth(bool $smooth = true)
    {
        $this->smooth = $smooth;

        return $this;
    }
}