<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

trait EnumHandlerConverter
{
    /**
     * @param $value
     *
     * @return string
     */
    protected function enumHandler($value)
    {
        if (!$value || !is_callable($value)) {
            return null;
        }

        return $value;
    }
}