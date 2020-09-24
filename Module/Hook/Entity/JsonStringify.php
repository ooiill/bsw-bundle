<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;

class JsonStringify extends Json
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        $space = $args['space'] ?? 4;
        $split = $args['split'] ?? ': ';

        return Helper::formatPrintJson(parent::preview($value, $args), $space, $split);
    }
}