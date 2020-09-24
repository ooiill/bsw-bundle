<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\AnnotationConverter;

/**
 * @property Mixed $item
 */
class MixedConverter extends AnnotationConverter
{
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function field($value)
    {
        return $value ?: $this->target;
    }
}