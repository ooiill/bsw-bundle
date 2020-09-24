<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Module\Filter\Filter;

class Like extends Filter
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function parse($value)
    {
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
        $name = $this->nameBuilder();

        return [
            "{$field} LIKE %:{$name}%",
            [$name => current($item)],
            [$name => Types::STRING],
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
        $value = current($item);

        return [
            $this->expr->like($field, $this->expr->literal("%{$value}%")),
        ];
    }
}