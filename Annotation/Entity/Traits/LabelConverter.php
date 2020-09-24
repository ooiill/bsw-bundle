<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait LabelConverter
{
    /**
     * @param $value
     *
     * @return string
     */
    protected function label($value)
    {
        if (!empty($value) && !is_string($value)) {
            $this->exception('label', 'should be string if configured');
        }

        if (is_string($value)) {
            return $value;
        }

        $label = explode('.', $this->item->field);
        $label = current(array_reverse($label));

        return Helper::stringToLabel(Helper::camelToUnder($label));
    }
}