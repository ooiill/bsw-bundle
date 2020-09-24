<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;
use Respect\Validation\Validator as v;

class Ip extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is ip address';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be ip address';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!v::ip()->validate($this->value)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->value;
    }
}