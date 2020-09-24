<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Hook\Hook;

class Json extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        if (!$value) {
            return null;
        }

        return Helper::parseJsonString($value, []);
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = Helper::jsonArray($value);
        }

        return Helper::jsonStringify($value);
    }
}