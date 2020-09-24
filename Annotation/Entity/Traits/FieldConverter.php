<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

trait FieldConverter
{
    /**
     * @param $value
     *
     * @return string
     */
    protected function field($value)
    {
        $value = $value ?: $this->item->value;
        if (empty($value) || !is_string($value)) {
            $this->exception('field', 'should be string');
        }

        return $value;
    }
}