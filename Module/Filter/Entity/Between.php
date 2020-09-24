<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Filter;

class Between extends Filter
{
    /**
     * @var bool
     */
    protected $timestamp = false;

    /**
     * @var bool
     */
    protected $carryTime = true;

    /**
     * @var bool
     */
    protected $weekValue = false;

    /**
     * @return bool
     */
    public function isTimestamp(): bool
    {
        return $this->timestamp;
    }

    /**
     * @param bool $timestamp
     *
     * @return $this
     */
    public function setTimestamp(bool $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCarryTime(): bool
    {
        return $this->carryTime;
    }

    /**
     * @param bool $carryTime
     *
     * @return $this
     */
    public function setCarryTime(bool $carryTime)
    {
        $this->carryTime = $carryTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWeekValue(): bool
    {
        return $this->weekValue;
    }

    /**
     * @param bool $weekValue
     *
     * @return $this
     */
    public function setWeekValue(bool $weekValue)
    {
        $this->weekValue = $weekValue;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return array
     * @throws
     */
    public function parse($value)
    {
        if ($this->isWeekValue()) {
            $value = Helper::yearWeekToDate(...explode('-', $value));
        }

        if (is_string($value)) {
            $value = Helper::stringToArray($value, false, false, 'trim', Abs::FORM_DATA_SPLIT);
        }

        if (!is_array($value) || count($value) < 2) {
            throw new FilterException(self::class . ' got invalid value for parse, given array please');
        }

        $from = trim($value[0]);
        $to = trim($value[1]);

        if (!$this->isCarryTime()) {
            $from .= Abs::_DAY_BEGIN;
            $to .= Abs::_DAY_END;
        }

        if (!$this->isTimestamp()) {
            return [$from, $to];
        }

        return [strtotime($from), strtotime($to)];
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
        [$from, $to] = $item;
        [$nameFrom, $nameTo] = $this->nameBuilder(['from', 'to']);

        return [
            "{$field} BETWEEN :{$nameFrom} AND :{$nameTo}",
            [
                $nameFrom => $from,
                $nameTo   => $to,
            ],
            [
                $nameFrom => is_numeric($from) ? Types::FLOAT : Types::STRING,
                $nameTo   => is_numeric($to) ? Types::FLOAT : Types::STRING,
            ],
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
        [$from, $to] = $item;
        [$nameFrom, $nameTo] = $this->nameBuilder(['from', 'to']);

        return [
            $this->expr->between($field, ":{$nameFrom}", ":{$nameTo}"),
            [
                $nameFrom => $from,
                $nameTo   => $to,
            ],
            [
                $nameFrom => is_numeric($from) ? Types::FLOAT : Types::STRING,
                $nameTo   => is_numeric($to) ? Types::FLOAT : Types::STRING,
            ],
        ];
    }
}