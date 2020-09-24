<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Validator\Validator;

class Order extends Validator
{
    /**
     * @var array
     */
    protected $sortType = [Abs::SORT_ASC, Abs::SORT_DESC];

    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is order type';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be order type {{ args }}';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!$this->value) {
            $this->value = current($this->args);
        }

        if (!$this->value) {
            $this->value = Abs::SORT_ASC;
        }

        if (!in_array($this->handler(), $this->sortType)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return strtoupper($this->value);
    }

    /**
     * @inheritdoc
     */
    public function isRequired(): bool
    {
        return false;
    }

    /**
     * Stringify args
     *
     * @return string
     */
    public function stringArgs(): string
    {
        return Helper::printArray($this->sortType, '[%s]', false);
    }
}