<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class HourDay extends Hook
{
    /**
     * @const int
     */
    const REDOUBLE = Abs::HEX_HOUR_DAY;

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        return $value / static::REDOUBLE;
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        return $value * static::REDOUBLE;
    }
}