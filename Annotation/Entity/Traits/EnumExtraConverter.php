<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

trait EnumExtraConverter
{
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function enumExtra($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (!empty($value) && is_string($value)) {
            return $value;
        }

        if ($value === true) {
            return $this->target;
        }

        return null;
    }
}