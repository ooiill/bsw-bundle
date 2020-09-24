<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;

class Money extends Hook
{
    /**
     * @const int
     */
    const REDOUBLE = 100;

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        $value /= static::REDOUBLE;

        return Helper::numberFormat($value, $args['decimals'] ?? 2);
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        return intval(bcmul(Helper::numericValue($value), static::REDOUBLE));
    }
}