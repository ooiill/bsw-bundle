<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Count
{
    /**
     * @var int
     */
    protected $count = 10;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setCount(int $count)
    {
        $this->count = $count;

        return $this;
    }
}