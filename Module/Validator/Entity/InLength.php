<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class InLength extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Length in';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Length must in value {{ args }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        $length = is_array($this->value) ? count($this->value) : Helper::strLen($this->value);

        return in_array($length, $this->args);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}