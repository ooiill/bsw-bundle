<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Same extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Same to';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must same to args {{ arg1 }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return $this->value == current($this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}