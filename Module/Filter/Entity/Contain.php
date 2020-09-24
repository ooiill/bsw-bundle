<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Filter;

class Contain extends Filter
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function parse($value)
    {
        if (is_string($value)) {
            $value = Helper::stringToArray($value, true, false, 'trim', Abs::FORM_DATA_SPLIT);
        }

        return $value;
    }

    /**
     * SQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     */
    public function sql(string $field, array $item): array
    {
        [$holder, $args, $types] = Helper::dqlInItems($item);

        return [
            "{$field} IN ({$holder})",
            $args,
            $types,
        ];
    }

    /**
     * DQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     */
    public function dql(string $field, array $item)
    {
        return [
            $this->expr->in($field, $item),
        ];
    }
}