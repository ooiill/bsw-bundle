<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class PositiveInt extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is positive integer';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be positive integer';
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

        return $this->value >= 1;
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return intval($this->value);
    }
}