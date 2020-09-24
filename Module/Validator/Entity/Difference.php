<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Difference extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Difference to';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must difference to args {{ arg1 }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return $this->value != current($this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}