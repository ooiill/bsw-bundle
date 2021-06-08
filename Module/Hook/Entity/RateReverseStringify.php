<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class RateReverseStringify extends Money
{
    /**
     * @const int
     */
    const REDOUBLE = 100;

    /**
     * @const int
     */
    const DECIMALS = 2;

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        $value *= static::REDOUBLE;
        $digit = $args['decimals'] ?? self::DECIMALS;

        return sprintf("%s %%", Helper::numberFormat($value, $digit));
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        $digit = $args['decimals'] ?? self::DECIMALS;

        return Helper::numberFormat(bcdiv($value, static::REDOUBLE), $digit);
    }
}