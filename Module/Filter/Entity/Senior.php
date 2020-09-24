<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Filter;

/**
 * @property Expr $expr
 */
class Senior extends Filter
{
    /**
     * @var int
     */
    protected $expression;

    /**
     * @const int
     */
    const EQ           = 1;
    const NEQ          = 2;
    const GT           = 3;
    const GTE          = 4;
    const LT           = 5;
    const LTE          = 6;
    const IN           = 7;
    const NOT_IN       = 8;
    const IS_NULL      = 9;
    const IS_NOT_NULL  = 10;
    const IS_BLANK     = 11;
    const IS_NOT_BLANK = 12;
    const IS_EMPTY     = 13;
    const IS_NOT_EMPTY = 14;
    const LIKE         = 15;
    const BEGIN_LIKE   = 16;
    const END_LIKE     = 17;
    const NOT_LIKE     = 18;
    const BETWEEN      = 19;

    /**
     * @const array
     */
    const MODE_EQ = [
        self::EQ  => 'Expr equal',
        self::NEQ => 'Expr not equal',
    ];

    /**
     * @const array
     */
    const MODE_GT = [
        self::GT  => 'Expr greater than',
        self::GTE => 'Expr greater than or equal to',
    ];

    /**
     * @const array
     */
    const MODE_LT = [
        self::LT  => 'Expr less than',
        self::LTE => 'Expr less than or equal to',
    ];

    /**
     * @const array
     */
    const MODE_LIKE = [
        self::LIKE       => 'Expr contain',
        self::BEGIN_LIKE => 'Expr begin contain',
        self::END_LIKE   => 'Expr end contain',
        self::NOT_LIKE   => 'Expr not contain',
    ];

    /**
     * @const array
     */
    const MODE_IN = [
        self::IN      => 'Expr in',
        self::NOT_IN  => 'Expr not in',
        self::BETWEEN => 'Expr between',
    ];

    /**
     * @const array
     */
    const MODE_EMPTY = [
        self::IS_NULL      => 'Expr is null',
        self::IS_NOT_NULL  => 'Expr is not null',
        self::IS_BLANK     => 'Expr is blank',
        self::IS_NOT_BLANK => 'Expr is not blank',
        self::IS_EMPTY     => 'Expr is empty',
        self::IS_NOT_EMPTY => 'Expr is not empty',
    ];

    /**
     * @const array
     */
    const MODE_SELECT_NUMBER = [
        self::EQ     => 'Expr equal',
        self::NEQ    => 'Expr not equal',
        self::GTE    => 'Expr greater than or equal to',
        self::LTE    => 'Expr less than or equal to',
        self::IN     => 'Expr in',
        self::NOT_IN => 'Expr not in',
    ];

    /**
     * @const array
     */
    const MODE_RANGE = [
        self::GT      => 'Expr greater than',
        self::GTE     => 'Expr greater than or equal to',
        self::LT      => 'Expr less than',
        self::LTE     => 'Expr less than or equal to',
        self::IN      => 'Expr in',
        self::NOT_IN  => 'Expr not in',
        self::BETWEEN => 'Expr between',
    ];

    /**
     * @const array
     */
    const MODE_FULL = [
        self::EQ           => 'Expr equal',
        self::NEQ          => 'Expr not equal',
        self::GT           => 'Expr greater than',
        self::GTE          => 'Expr greater than or equal to',
        self::LT           => 'Expr less than',
        self::LTE          => 'Expr less than or equal to',
        self::LIKE         => 'Expr contain',
        self::BEGIN_LIKE   => 'Expr begin contain',
        self::END_LIKE     => 'Expr end contain',
        self::NOT_LIKE     => 'Expr not contain',
        self::IN           => 'Expr in',
        self::NOT_IN       => 'Expr not in',
        self::BETWEEN      => 'Expr between',
        self::IS_NULL      => 'Expr is null',
        self::IS_NOT_NULL  => 'Expr is not null',
        self::IS_BLANK     => 'Expr is blank',
        self::IS_NOT_BLANK => 'Expr is not blank',
        self::IS_EMPTY     => 'Expr is empty',
        self::IS_NOT_EMPTY => 'Expr is not empty',
    ];

    /**
     * @param mixed $value
     *
     * @return array
     * @throws
     */
    public function parse($value): array
    {
        if (empty($this->expression = $value[0] ?? null)) {
            throw new FilterException("Give filter expression first");
        }

        if (!isset(self::MODE_FULL[$this->expression])) {
            throw new FilterException("Filter expression is not support");
        }

        if (is_null($value = $value[1] ?? null)) {
            if (!in_array(
                $this->expression,
                [
                    self::IS_NULL,
                    self::IS_NOT_NULL,
                    self::IS_BLANK,
                    self::IS_NOT_BLANK,
                    self::IS_EMPTY,
                    self::IS_NOT_EMPTY,
                ]
            )) {
                throw new FilterException("Value for filter expression is required");
            }
            $value = '';
        }

        if (is_scalar($value)) {
            $value = Helper::stringToArray($value, false, false, null, Abs::FORM_DATA_SPLIT);
        }

        return Helper::numericValues($value);
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
        [$targetKey, $firstKey, $secondKey] = $this->nameBuilder(['target', 'first', 'second']);

        $target = $item[0] ?? null;
        $first = $target;
        $second = $item[1] ?? null;

        if ($this->expression == self::EQ) {
            return [
                "{$field} = :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::NEQ) {
            return [
                "{$field} <> :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::GT) {
            return [
                "{$field} > :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::GTE) {
            return [
                "{$field} >= :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::LT) {
            return [
                "{$field} < :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::LTE) {
            return [
                "{$field} <= :{$targetKey}",
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::IN) {
            [$holder, $params, $types] = Helper::sqlInItems($item);

            return [
                "{$field} IN ({$holder})",
                $params,
                $types,
            ];
        }

        if ($this->expression == self::NOT_IN) {
            [$holder, $params, $types] = Helper::sqlInItems($item);

            return [
                "{$field} NOT IN ({$holder})",
                $params,
                $types,
            ];
        }

        if ($this->expression == self::IS_NULL) {
            return ["{$field} IS NULL"];
        }

        if ($this->expression == self::IS_NOT_NULL) {
            return ["{$field} IS NOT NULL"];
        }

        if ($this->expression == self::IS_BLANK) {
            return ["{$field} = ''"];
        }

        if ($this->expression == self::IS_NOT_BLANK) {
            return ["{$field} <> ''"];
        }

        if ($this->expression == self::IS_EMPTY) {
            return ["({$field} IS NULL OR {$field} = '')"];
        }

        if ($this->expression == self::IS_NOT_EMPTY) {
            return ["({$field} IS NOT NULL AND {$field} <> '')"];
        }

        if ($this->expression == self::LIKE) {
            return [
                "{$field} LIKE '%:{$targetKey}%'",
                [$targetKey => $target],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::BEGIN_LIKE) {
            return [
                "{$field} LIKE ':{$targetKey}%'",
                [$targetKey => $target],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::END_LIKE) {
            return [
                "{$field} LIKE '%:{$targetKey}'",
                [$targetKey => $target],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::NOT_LIKE) {
            return [
                "{$field} NOT LIKE '%:{$targetKey}%'",
                [$targetKey => $target],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::BETWEEN) {

            if (is_null($second)) {
                throw new FilterException('The filter arguments is not enough');
            }

            return [
                "{$field} BETWEEN :{$firstKey} AND :{$secondKey}",
                [
                    $firstKey  => $first,
                    $secondKey => $second,
                ],
                [
                    $firstKey  => is_numeric($first) ? Types::FLOAT : Types::STRING,
                    $secondKey => is_numeric($second) ? Types::FLOAT : Types::STRING,
                ],
            ];
        }

        return [null];
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
        [$targetKey, $firstKey, $secondKey] = $this->nameBuilder(['target', 'first', 'second']);

        $target = $item[0] ?? null;
        $first = $target;
        $second = $item[1] ?? null;

        if ($this->expression == self::EQ) {
            return [
                $this->expr->eq($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::NEQ) {
            return [
                $this->expr->neq($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::GT) {
            return [
                $this->expr->gt($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::GTE) {
            return [
                $this->expr->gte($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::LT) {
            return [
                $this->expr->lt($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::LTE) {
            return [
                $this->expr->lte($field, ":{$targetKey}"),
                [$targetKey => $target],
                [$targetKey => is_numeric($target) ? Types::FLOAT : Types::STRING],
            ];
        }

        if ($this->expression == self::IN) {
            return [$this->expr->in($field, $item)];
        }

        if ($this->expression == self::NOT_IN) {
            return [$this->expr->notIn($field, $item)];
        }

        if ($this->expression == self::IS_NULL) {
            return [$this->expr->isNull($field)];
        }

        if ($this->expression == self::IS_NOT_NULL) {
            return [$this->expr->isNotNull($field)];
        }

        if ($this->expression == self::IS_BLANK) {
            return [
                $this->expr->eq($field, ":{$targetKey}"),
                [$targetKey => ''],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::IS_NOT_BLANK) {
            return [
                $this->expr->neq($field, ":{$targetKey}"),
                [$targetKey => ''],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::IS_EMPTY) {
            return [
                $this->expr->orX(
                    $this->expr->isNull($field),
                    $this->expr->eq($field, ":{$targetKey}")
                ),
                [$targetKey => ''],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::IS_NOT_EMPTY) {
            return [
                $this->expr->andX(
                    $this->expr->isNotNull($field),
                    $this->expr->neq($field, ":{$targetKey}")
                ),
                [$targetKey => ''],
                [$targetKey => Types::STRING],
            ];
        }

        if ($this->expression == self::LIKE) {
            return [
                $this->expr->like($field, $this->expr->literal("%{$target}%")),
            ];
        }

        if ($this->expression == self::BEGIN_LIKE) {
            return [
                $this->expr->like($field, $this->expr->literal("{$target}%")),
            ];
        }

        if ($this->expression == self::END_LIKE) {
            return [
                $this->expr->like($field, $this->expr->literal("%{$target}")),
            ];
        }

        if ($this->expression == self::NOT_LIKE) {
            return [
                $this->expr->notLike($field, $this->expr->literal("%{$target}%")),
            ];
        }

        if ($this->expression == self::BETWEEN) {

            if (is_null($second)) {
                throw new FilterException('The filter arguments is not enough');
            }

            return [
                $this->expr->between($field, ":{$firstKey}", ":{$secondKey}"),
                [
                    $firstKey  => $first,
                    $secondKey => $second,
                ],
                [
                    $firstKey  => is_numeric($first) ? Types::FLOAT : Types::STRING,
                    $secondKey => is_numeric($second) ? Types::FLOAT : Types::STRING,
                ],
            ];
        }

        return [null];
    }
}