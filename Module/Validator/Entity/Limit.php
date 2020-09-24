<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Limit extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Length between';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Length must between {{ arg1 }} and {{ arg2 }}';
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
        $length = is_array($this->value) ? count($this->value) : Helper::strLen($this->value);

        return $length >= $this->args[0] && $length <= $this->args[1];
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}