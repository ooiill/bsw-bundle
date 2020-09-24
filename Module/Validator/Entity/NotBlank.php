<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class NotBlank extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Not blank';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must not blank';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return trim($this->value) != '';
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}