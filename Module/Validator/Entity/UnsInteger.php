<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class UnsInteger extends Validator
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
        return Helper::isIntNumeric($this->value) && $this->value >= 0;
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return intval($this->value);
    }
}