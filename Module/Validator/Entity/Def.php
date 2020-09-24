<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Def extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Set default value';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        return (trim($this->value) === '' || is_null($this->value));
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return current($this->args);
    }

    /**
     * @inheritdoc
     */
    public function isRequired(): bool
    {
        return false;
    }
}