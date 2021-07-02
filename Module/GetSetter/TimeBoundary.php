<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait TimeBoundary
{
    /**
     * @var string
     */
    protected $timeHead = Abs::DAY_BEGIN;

    /**
     * @var string
     */
    protected $timeTail = Abs::DAY_END;

    /**
     * @return string
     */
    public function getTimeHead(): string
    {
        return $this->timeHead;
    }

    /**
     * @param string $timeHead
     *
     * @return $this
     */
    public function setTimeHead(string $timeHead)
    {
        $this->timeHead = $timeHead;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeTail(): string
    {
        return $this->timeTail;
    }

    /**
     * @param string $timeTail
     *
     * @return $this
     */
    public function setTimeTail(string $timeTail)
    {
        $this->timeTail = $timeTail;

        return $this;
    }
}