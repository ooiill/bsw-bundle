<?php

namespace Leon\BswBundle\Module\Filter\Entity;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Module\Filter\Filter;

class TeamMember extends Filter
{
    /**
     * @var array
     */
    protected $alias = ['team' => ['x', 'teamId'], 'member' => ['t', 'memberId']];

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
     * @param mixed $value
     *
     * @return array
     */
    public function parse($value)
    {
        $value = explode('-', $value) + [0, 0];
        $value = array_map('intval', $value);

        return $value;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    protected function fieldElection(array $value): array
    {
        $value = array_combine(['team', 'member'], $value);
        if (!empty($value['member'])) {
            return [implode('.', $this->getAlias('member')), $value['member']];
        }

        return [implode('.', $this->getAlias('team')), $value['team']];
    }

    /**
     * Correct team member by agency
     *
     * @param int    $team
     * @param string $value
     *
     * @return string
     */
    public function correctTeamMemberByAgency(int $team, ?string $value = null): string
    {
        if (empty($value)) {
            return "{$team}";
        }

        $valueHandling = $this->parse($value);
        $valueHandling = array_combine(['team', 'member'], $valueHandling);

        if ($valueHandling['team'] != $team) {
            return "{$team}";
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
        [$field, $value] = $this->fieldElection($item);
        $valueName = $this->nameBuilder('value');

        return [
            "{$field} = :{$valueName}",
            [$valueName => $value],
            [$valueName => Types::INTEGER],
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
        [$field, $value] = $this->fieldElection($item);
        $valueName = $this->nameBuilder('value');

        return [
            $this->expr->eq($field, ":{$valueName}"),
            [$valueName => $value],
            [$valueName => Types::INTEGER],
        ];
    }
}