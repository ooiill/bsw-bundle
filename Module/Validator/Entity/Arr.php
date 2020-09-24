<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Arr extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is array';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be array';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return is_array($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}