<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Validator\Validator;

class IdString extends Validator
{
    /**
     * @var array
     */
    protected $ids = [];

    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Id string';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be id string split by comma';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        $this->ids = Helper::stringToArray($this->value, true, true, 'intval');
        $this->ids = array_filter($this->ids);

        return !empty($this->ids);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->ids;
    }
}