<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;

class Trim extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Trim';
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
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        $charList = empty($this->args) ? null : current($this->args);

        return $charList ? trim($this->value, $charList) : trim($this->value);
    }
}