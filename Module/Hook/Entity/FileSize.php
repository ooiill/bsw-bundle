<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;

class FileSize extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        if (!Helper::isIntNumeric($value)) {
            return null;
        }

        return Helper::humanSize($value, $args['decimals'] ?? 1);
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        return $value;
    }
}