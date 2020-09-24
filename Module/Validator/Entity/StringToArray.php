<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class StringToArray extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'String to array';
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
        $split = current($this->args) ?: ',';
        
        if (empty($this->value)) {
            return $this->value;
        }

        return Helper::stringToArray($this->value, true, true, null, $split);
    }
}