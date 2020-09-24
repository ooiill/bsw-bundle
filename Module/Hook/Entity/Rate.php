<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class Rate extends Money
{
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
}