<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Integer extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is integer';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be integer';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return Helper::isIntNumeric($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return intval($this->value);
    }
}