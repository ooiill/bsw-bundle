<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Validator\Validator;

class MysqlSmallint extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is mysql smallint';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must between {{ arg1 }} and {{ arg2 }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!Helper::isIntNumeric($this->value)) {
            return false;
        }

        if ($this->args && in_array($this->value, $this->args)) {
            return true;
        }

        return $this->value >= Abs::MYSQL_SMALLINT_MIN && $this->value <= Abs::MYSQL_SMALLINT_MAX;
    }

    /**
     * @inheritdoc
     */
    public function arrayArgs(): array
    {
        return [Abs::MYSQL_SMALLINT_MIN, Abs::MYSQL_SMALLINT_MAX];
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return intval($this->value);
    }
}