<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\AnnotationConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumExtraConverter;
use Leon\BswBundle\Annotation\Entity\Traits\EnumHandlerConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FormTypeArgsConverter;
use Leon\BswBundle\Annotation\Entity\Traits\FormTypeConverter;
use Leon\BswBundle\Annotation\Entity\Traits\HookConverter;
use Leon\BswBundle\Annotation\Entity\Traits\TransConverter;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Filter\Entity\Accurate;
use Leon\BswBundle\Module\Filter\Entity\Like;
use Symfony\Component\Validator\Constraints\Type;
use Leon\BswBundle\Module\Filter\Filter as BswFilter;

/**
 * @property Filter $item
 */
class FilterConverter extends AnnotationConverter
{
    use HookConverter;
    use EnumConverter;
    use EnumExtraConverter;
    use EnumHandlerConverter;
    use TransConverter;
    use FormTypeConverter;
    use FormTypeArgsConverter;

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function field($value)
    {
        return $value ?: $this->item->value ?: $this->target;
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws
     */
    protected function filter($value)
    {
        if (!isset($value)) {
            $type = $this->items[Type::class]->type ?? null;
            if (strpos($type, 'int') !== false) {
                $value = Accurate::class;
            } else {
                $value = Like::class;
            }
        }

        $class = BswFilter::class;
        if (!Helper::extendClass($value, $class)) {
            $this->exception('type', "Must be class extend {$class}");
        }

        return new $value;
    }

    /**
     * @param $value
     *
     * @return array
     * @throws
     */
    protected function filterArgs($value)
    {
        if (empty($value)) {
            return [];
        }

        if (!is_array($value)) {
            $this->exception('filterArgs', 'must be array type');
        }

        $filter = $this->item->filter;
        foreach ($value as $key => $val) {
            if (is_int($key)) {
                [$key, $val] = [$val, null];
            }
            $fn = 'set' . Helper::underToCamel($key, false);
            if (!method_exists($filter, $fn)) {
                $this->exception(
                    'filterArgs',
                    "item named `{$key}` don't exists class attribute in " . get_class($filter)
                );
            }
            $filter->{$fn}($val);
        }

        return $value;
    }

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
     * @return mixed
     * @throws
     */
    protected function column($value)
    {
        $max = 10;

        if (isset($value)) {
            if ($value < 1 || $value > $max) {
                $this->exception('column', "Must be integer between 1 and {$max}");
            }

            return intval($value);
        }

        return 2;
    }
}
