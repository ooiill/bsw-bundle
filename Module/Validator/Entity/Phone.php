<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Respect\Validation\Validator as v;
use Leon\BswBundle\Module\Validator\Validator;

class Phone extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is phone number';
    }
    
    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be phone number';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return v::phone()->validate($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return strval($this->value);
    }
}