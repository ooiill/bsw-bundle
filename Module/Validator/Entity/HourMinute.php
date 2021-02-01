<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class HourMinute extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is hour-minute format';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Standard must be hour-minute format';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        $split = $this->args[0] ?? ':';
        if (!preg_match("/^(20|21|22|23|[0-1]\d){$split}[0-5]\d$/", $this->value)) {
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