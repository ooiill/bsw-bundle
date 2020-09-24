<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class InKey extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'In array\'key';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must in key {{ args }}';
    }

    /**
     * @inheritdoc
     */
    protected function proveArgs(): bool
    {
        return is_array($this->args);
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return isset($this->args[$this->value]);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }

    /**
     * Stringify args
     *
     * @return string
     */
    public function stringArgs(): string
    {
        return Helper::printArray($this->args);
    }
}