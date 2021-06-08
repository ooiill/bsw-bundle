<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class RateStringify extends Money
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
        $digit = $args['decimals'] ?? 2;

        return sprintf("%s %%", Helper::numberFormat($value, $digit));
    }
}