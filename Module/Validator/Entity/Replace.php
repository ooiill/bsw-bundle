<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Replace extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Replace string';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be string';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return is_string($this->value);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        if (empty($this->args[0])) {
            return $this->value;
        }

        $from = $this->args[0];
        $to = $this->args[1] ?? null;

        return str_replace($from, $to, $this->value);
    }
}