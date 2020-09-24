<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\AnnotationConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FieldConverter;
use Leon\BswBundle\Annotation\Entity\Traits\LabelConverter;
use Leon\BswBundle\Annotation\Entity\Traits\TransConverter;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Output $item
 */
class OutputConverter extends AnnotationConverter
{
    use FieldConverter;
    use LabelConverter;
    use TransConverter;

    /**
     * @param $value
     *
     * @return string
     * @throws
     */
    protected function type($value)
    {
        if (empty($value) || !is_string($value)) {
            $this->exception('type', 'is required string');
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return null|string
     */
    protected function extra($value)
    {
        if (empty($value)) {
            return null;
        }

        return Abs::FN_API_DOC_OUTPUT . ucfirst($value);
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function enum($value)
    {
        return (array)$value;
    }
}