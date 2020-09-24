<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait ValidatorConverter
{
    /**
     * @param $value
     *
     * @return false|string
     */
    protected function validator($value)
    {
        if (!is_scalar($value) || !$value) {
            return false;
        }

        if ($value === true) {
            $value = Helper::underToCamel($this->item->field);
        }

        return "{$value}Validator";
    }
}