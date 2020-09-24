<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Json extends Validator
{
    /**
     * @var array
     */
    protected $jsonArray;

    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is json stringify';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be json string format';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!empty($this->value) && !is_string($this->value)) {
            return false;
        }

        $this->jsonArray = Helper::parseJsonString($this->value ?? '');

        return is_array($this->jsonArray);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->jsonArray;
    }
}