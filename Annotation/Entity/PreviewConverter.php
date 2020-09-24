<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\AnnotationConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumExtraConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumHandlerConverter;
use Leon\BswBundle\Annotation\Entity\Traits\HookConverter;
use Leon\BswBundle\Annotation\Entity\Traits\TransConverter;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @property Preview $item
 */
class PreviewConverter extends AnnotationConverter
{
    use HookConverter;
    use EnumConverter;
    use EnumExtraConverter;
    use EnumHandlerConverter;
    use TransConverter;

    /**
     * @param $value
     *
     * @return string
     */
    protected function label($value)
    {
        return $value ?: Helper::stringToLabel(Helper::camelToUnder($this->target));
    }

    /**
     * @param $value
     *
     * @return string|null
     */
    protected function align($value)
    {
        if (in_array($value, [Abs::POS_CENTER, Abs::POS_LEFT, Abs::POS_RIGHT])) {
            return $value;
        }

        $type = $this->items[Type::class]->type ?? null;
        if ($type == Abs::T_INTEGER) {
            return Abs::POS_CENTER;
        }

        return null;
    }

    /**
     * @param $value
     *
     * @return string
     * @throws
     */
    protected function width($value)
    {
        if (!empty($value)) {
            if (!is_numeric($value)) {
                $this->exception('width', 'should be numeric');
            }

            return $value;
        }

        /**
         * enum
         */
        if ($this->item->enum) {
            return 160;
        }

        /**
         * integer
         */
        $type = $this->items[Type::class]->type ?? null;
        if ($type == Abs::T_INTEGER || $type == Abs::T_NUMERIC) {
            return 140;
        }

        if ($type == Abs::T_INTEGER) {
            return 160;
        }

        $min = 80;
        $max = 320;
        $step = 10;

        /**
         * string
         */
        $length = $this->items[Length::class]->max ?? null;
        if (!$length) {
            return $max;
        }

        $width = $length * $step;

        if ($width < $min) {
            return $min;
        }

        if ($width > $max) {
            return $max;
        }

        return $width;
    }
}