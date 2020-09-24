<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Between extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Between';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must between {{ arg1 }} and {{ arg2 }}';
    }

    /**
     * @inheritdoc
     */
    protected function proveArgs(): bool
    {
        return is_numeric($this->args[0] ?? null) && is_numeric($this->args[1] ?? null);
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return $this->value >= $this->args[0] && $this->value <= $this->args[1];
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}