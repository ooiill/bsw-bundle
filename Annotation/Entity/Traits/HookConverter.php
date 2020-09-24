<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

trait HookConverter
{
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function hook($value)
    {
        return (array)$value;
    }
}