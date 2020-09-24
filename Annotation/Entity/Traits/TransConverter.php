<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

trait TransConverter
{
    /**
     * @param $value
     *
     * @return bool
     */
    protected function trans($value)
    {
        if (!isset($value)) {
            return true;
        }

        return !!$value;
    }
}