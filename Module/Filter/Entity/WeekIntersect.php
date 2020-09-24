<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;

class WeekIntersect extends Between
{
    /**
     * @var bool
     */
    protected $weekValue = true;

    /**
     * @var array
     */
    protected $alias = ['from' => 'x.startTime', 'to' => 'x.endTime'];

    /**
     * @param string $index
     *
     * @return array|string
     */
    public function getAlias(string $index = null)
    {
        return $index ? ($this->alias[$index] ?? null) : $this->alias;
    }

    /**
     * @param array $alias
     *
     * @return $this
     */
    public function setAlias(array $alias)
    {
        $this->alias = array_merge($this->alias, $alias);

        return $this;
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
        [$fromName, $toName] = $this->nameBuilder(['from', 'to']);

        $fromField = $this->getAlias('from');
        $toField = $this->getAlias('to');

        return [
            "({$fromField} BETWEEN :{$fromName} AND :{$fromName}) OR ({$toField} BETWEEN :{$fromName} AND :{$fromName}) OR ({$fromField} < :{$fromName} AND {$toField} > :{$toName})",
            [
                $fromName => $from,
                $toName   => $to,
            ],
            [
                $fromName => is_numeric($from) ? Types::FLOAT : Types::STRING,
                $toName   => is_numeric($to) ? Types::FLOAT : Types::STRING,
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
        [$fromName, $toName] = $this->nameBuilder(['from', 'to']);

        $fromField = $this->getAlias('from');
        $toField = $this->getAlias('to');


        return [
            $this->expr->orX(
                $this->expr->between($fromField, ":{$fromName}", ":{$toName}"),
                $this->expr->between($toField, ":{$fromName}", ":{$toName}"),
                $this->expr->andX(
                    $this->expr->lt($fromField, ":{$fromName}"),
                    $this->expr->gt($toField, ":{$toName}")
                )
            ),
            [
                $fromName => $from,
                $toName   => $to,
            ],
            [
                $fromName => is_numeric($from) ? Types::FLOAT : Types::STRING,
                $toName   => is_numeric($to) ? Types::FLOAT : Types::STRING,
            ],
        ];
    }
}