<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Lt extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Less than';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must less than {{ arg1 }}';
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
        return $this->value < current($this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return Helper::numericValue($this->value);
    }
}