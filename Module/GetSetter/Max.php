<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait Max
{
    /**
     * @var float|int
     */
    protected $max = Abs::MYSQL_INT_UNS_MAX;

    /**
     * @return float|int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param $max
     *
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }
}