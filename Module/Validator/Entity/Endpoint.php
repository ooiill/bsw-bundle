<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;
use Respect\Validation\Validator as v;

class Endpoint extends Validator
{
    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Is endpoint string';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Must be endpoint string';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        list($ip, $port) = explode(':', $this->value) + [1 => 0];

        if (!v::ip()->validate($ip)) {
            return false;
        }

        if (!is_numeric($port)) {
            return false;
        }

        $port = intval($port);
        if ($port < 1 || $port > 65535) {
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