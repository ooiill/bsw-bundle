<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class Truncate extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Truncate';
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
        $max = empty($this->args) ? 0 : intval(current($this->args));
        if ($max <= 2) {
            return $this->value;
        }

        return Helper::mSubStr($this->value, 0, $max - 2);
    }
}