<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Filter;

class Mixed extends Filter
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

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
     * Parse search field
     *
     * @param $value
     *
     * @return array
     * @throws
     */
    protected function parseSearchField($value): array
    {
        if (!$this->getFields()) {
            throw new FilterException('Configured the target fields of search please');
        }

        $fields = [];
        foreach ($this->getFields() as $key => $val) {
            if (is_numeric($key)) {
                $fields[$val] = null;
            } else {
                $fields[$key] = $val;
            }
        }

        $matchSuccess = [];
        $matchNull = [];
        $matchFailed = [];

        foreach ($fields as $field => $reg) {
            if (!$reg) {
                array_push($matchNull, $field);
            } elseif (preg_match($reg, $value)) {
                array_push($matchSuccess, $field);
            } else {
                array_push($matchFailed, $field);
            }
        }

        if (!empty($matchSuccess)) {
            return $matchSuccess;
        }

        if (!empty($matchNull)) {
            return $matchNull;
        }

        throw new FilterException('Please give qualified input');
    }

    /**
     * SQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     * @throws
     */
    public function sql(string $field, array $item): array
    {
        $expr = [];
        $value = current($item);
        $name = $this->nameBuilder();

        foreach ($this->parseSearchField($value) as $f) {
            array_push($expr, "{$f} LIKE %:{$name}%");
        }

        return [
            implode(' OR ', $expr),
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
     * @throws
     */
    public function dql(string $field, array $item)
    {
        $expr = [];
        $value = current($item);

        foreach ($this->parseSearchField($value) as $f) {
            array_push($expr, $this->expr->like($f, $this->expr->literal("%{$value}%")));
        }

        return [
            $this->expr->orX(...$expr),
        ];
    }
}