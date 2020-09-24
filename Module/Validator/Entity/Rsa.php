<?php

namespace Leon\BswBundle\Module\Validator\Entity;

use Leon\BswBundle\Module\Validator\Validator;
use Leon\BswBundle\Component\Rsa as ComponentRsa;

class Rsa extends Validator
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return 'Rsa';
    }

    /**
     * @inheritdoc
     */
    public function message(): string
    {
        return '{{ field }} Crypt text illegal';
    }

    /**
     * @inheritdoc
     */
    protected function prove(array $extra = []): bool
    {
        if (!($rsa = $extra[self::class] ?? false)) {
            return false;
        }

        /**
         * @var ComponentRsa $rsa
         */
        $this->text = $rsa->decryptByPrivateKey($this->value);

        return !is_null($this->text);
    }

    /**
     * @inheritdoc
     */
    protected function handler()
    {
        return $this->text;
    }
}