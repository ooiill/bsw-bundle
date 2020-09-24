<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Str extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is string';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be string';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return is_string($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return strval($this->value);
    }
}