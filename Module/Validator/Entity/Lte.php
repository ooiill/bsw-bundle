<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Lte extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Less than or equal to';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must less than or equal to {{ arg1 }}';
    }

    /**
     * @inheritdoc
     */
    protected function proveArgs(): bool
    {
        return is_numeric(current($this->args));
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return $this->value <= current($this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return Helper::numericValue($this->value);
    }
}