<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Password extends Validator
{
    /**
     * @var int
     */
    protected $minLength = 8;

    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is standard password';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Standard must be password';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!preg_match('/[a-z]/', $this->value)) {
            return false;
        }

        if (!preg_match('/[A-Z]/', $this->value)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $this->value)) {
            return false;
        }

        if (Helper::strLen($this->value) < $this->minLength) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function arrayArgs(): array
    {
        return [$this->minLength];
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return strval($this->value);
    }
}