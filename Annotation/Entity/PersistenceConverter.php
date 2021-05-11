<?php

namespace Leon\BswBundle\Annotation\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Leon\BswBundle\Annotation\AnnotationConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumExtraConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumHandlerConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FormTypeArgsConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FormTypeConverter;
use Leon\BswBundle\Annotation\Entity\Traits\HookConverter;
use Leon\BswBundle\Annotation\Entity\Traits\RulesConverter;
use Leon\BswBundle\Annotation\Entity\Traits\TransConverter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Date;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Radio;
use Leon\BswBundle\Module\Form\Entity\Score;
use Leon\BswBundle\Module\Form\Entity\Slider;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @property Persistence $item
 */
class PersistenceConverter extends AnnotationConverter
{
    use HookConverter;
    use EnumConverter;
    use EnumExtraConverter;
    use EnumHandlerConverter;
    use TransConverter;
    use FormTypeConverter;
    use FormTypeArgsConverter;
    use RulesConverter;

    /**
     * @var array
     */
    protected $fullWidthForm = [
        Checkbox::class,
        Radio::class,
        Score::class,
        Slider::class,
    ];

    /**
     * @param $value
     *
     * @return bool
     */
    protected function hide($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (isset($this->items[Id::class])) {
            return true;
        }

        return false;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function label($value)
    {
        if ($value === false) {
            return '';
        }

        return $value ?: Helper::stringToLabel(Helper::camelToUnder($this->target));
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws
     */
    protected function column($value)
    {
        $totalColumn = Abs::PERSISTENCE_TOTAL_COLUMN;
        $labelColumn = Abs::PERSISTENCE_LABEL_COLUMN;

        $max = $totalColumn - $labelColumn;
        $moderate = $max < 8 ? $max : 8;

        if (isset($value)) {
            if ($value < 1 || $value > $max) {
                $this->exception('column', "Must be integer between 1 and {$max}");
            }

            return intval($value);
        }

        $form = get_class($this->item->type);
        if (in_array($form, $this->fullWidthForm)) {
            return $max;
        }

        if ($form == Date::class) {
            return $moderate;
        }

        $type = $this->items[Type::class]->type ?? null;
        if ($type == 'numeric') {
            return $moderate + 2;
        }

        if (strpos($type, 'int') !== false) {
            return $moderate;
        }

        /**
         * string
         */
        $length = $this->items[Length::class]->max ?? null;
        if ($length = ceil($length / 3)) {
            if ($length > $max) {
                return $max;
            }

            if ($length < $moderate) {
                return $moderate;
            }

            return $length;
        }

        return $max;
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function style($value)
    {
        if (!is_array($value)) {
            return [];
        }

        if (!empty($value)) {
            return $value;
        }

        if (in_array(get_class($this->item->type), $this->fullWidthForm)) {
            $value['width'] = '100%';
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function formRules($value)
    {
        if ($value === false) {
            return [];
        }

        $value = (array)$value;
        $notNull = $this->items[NotNull::class] ?? null;
        $pk = $this->items[Id::class] ?? null;

        if ($notNull && !$pk && $this->value !== '') {
            array_push($value, Abs::RULES_REQUIRED);
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function validatorType($value)
    {
        if ($value) {
            return $value;
        }

        return $this->items[Column::class]->type ?? Abs::T_STRING;
    }
}