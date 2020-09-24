<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Hook\Hook;

class DefaultTimestamp extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        if (is_int($value) && $value > 0) {
            return $value;
        }

        return time();
    }
}