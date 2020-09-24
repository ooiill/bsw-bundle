<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class NotEmpty extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Not empty';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must not empty';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return !empty($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}