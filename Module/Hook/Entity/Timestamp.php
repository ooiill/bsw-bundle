<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;

class Timestamp extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        $scene = $args['scene'] ?? null;
        if ($scene === Abs::TAG_FILTER && empty($value)) {
            return null;
        }

        if ($value == 0) {
            $zero = trim("{$scene}_zero", '_');
            if (!empty($args[$zero])) { // default value
                return $args[$zero];
            }
        }

        if (empty($value)) {
            $empty = trim("{$scene}_empty", '_');
            if (!empty($args[$empty])) { // default timestamp
                $value = $args[$empty];
            }
        }

        return date($args['format'] ?? Abs::FMT_FULL, $value);
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        if (!empty($value)) {
            return strtotime($value);
        }

        return $value ?: time();
    }
}