<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Numeric extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is numeric';
    }
    
    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be numeric';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return is_numeric($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return Helper::numericValue($this->value);
    }
}