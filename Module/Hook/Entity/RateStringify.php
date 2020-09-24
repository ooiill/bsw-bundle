<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class RateStringify extends Rate
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
        $tpl = $args['tpl'] ?? '%.2f %%';

        return sprintf($tpl, Helper::numberFormat($value, $args['decimals'] ?? 2));
    }
}