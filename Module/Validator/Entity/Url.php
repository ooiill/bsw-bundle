<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Respect\Validation\Validator as v;
use Leon\BswBundle\Module\Validator\Validator;

class Url extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is url';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be url';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return v::url()->validate($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}