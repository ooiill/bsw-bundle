<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Respect\Validation\Validator as v;
use Leon\BswBundle\Module\Validator\Validator;

class Email extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is email';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be email';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return v::email()->validate($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}