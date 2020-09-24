<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Module\Filter\Filter;

class Accurate extends Filter
{
    /**
     * @param mixed $value
     *
     * @return string|array
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
        $value = current($item);

        return [
            "{$field} = :{$name}",
            [$name => $value],
            [$name => is_numeric($value) ? Types::FLOAT : Types::STRING],
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
        $name = $this->nameBuilder();
        $value = current($item);

        return [
            $this->expr->eq($field, ":{$name}"),
            [$name => $value],
            [$name => is_numeric($value) ? Types::FLOAT : Types::STRING],
        ];
    }
}