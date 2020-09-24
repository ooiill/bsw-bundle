<?php

namespace Leon\BswBundle\Module\Filter;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;

abstract class Filter
{
    /**
     * @var Expr $expr
     */
    protected $expr;

    /**
     * @var bool
     */
    protected $having = false;

    /**
     * Filter constructor.
     */
    public function __construct()
    {
        $this->expr = new Expr();
    }

    /**
     * @return Expr
     */
    public function getExpr(): Expr
    {
        return $this->expr;
    }

    /**
     * @param Expr $expr
     */
    public function setExpr(Expr $expr): void
    {
        $this->expr = $expr;
    }

    /**
     * @return bool
     */
    public function isHaving(): bool
    {
        return $this->having;
    }

    /**
     * @param bool $having
     *
     * @return $this
     */
    public function setHaving(bool $having)
    {
        $this->having = $having;

        return $this;
    }

    /**
     * Parse
     *
     * @param mixed $value
     *
     * @return mixed
     */
    abstract protected function parse($value);

    /**
     * SQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return array
     */
    abstract protected function sql(string $field, array $item): array;

    /**
     * DQL
     *
     * @param string $field
     * @param array  $item
     *
     * @return mixed
     */
    abstract protected function dql(string $field, array $item);

    /**
     * Bound name builder
     *
     * @param mixed $prefix
     *
     * @return mixed
     */
    protected function nameBuilder($prefix = '')
    {
        $random = Helper::generateToken(8, 36);

        $name = [];
        foreach ((array)$prefix as $item) {
            $item && $item = Helper::camelToUnder($item);
            array_push($name, "{$item}_{$random}");
        }

        return is_array($prefix) ? $name : current($name);
    }
}