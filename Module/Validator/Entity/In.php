<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class In extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'In array\'value';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must in {{ args }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return in_array($this->value, $this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    protected function transArgs(): bool
    {
        return false;
    }
}