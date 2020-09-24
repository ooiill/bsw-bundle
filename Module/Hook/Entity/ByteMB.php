<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class ByteMB extends Hook
{
    /**
     * @const int
     */
    const REDOUBLE = Abs::HEX_SIZE_2;

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        if (is_null($value)) {
            return null;
        }

        return Helper::numberFormat($value / static::REDOUBLE, 2);
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        return intval($value) * static::REDOUBLE;
    }
}