<?php

namespace Leon\BswBundle\Module\Validator;

final class ValidatorResult
{
    /**
     * @var array
     */
    public $error = [];

    /**
     * @var mixed
     */
    public $args;

    /**
     * @var mixed
     */
    public $sign;

    /**
     * ValidatorResult constructor.
     *
     * @param mixed $args
     * @param mixed $sign
     * @param array $error
     */
    public function __construct($args, $sign = null, array $error = [])
    {
        $this->args = $args;
        $this->sign = $sign ?? $this->args;
        $this->error = $error;
    }

    /**
     * @param string $error
     */
    public function error(string $error)
    {
        array_push($this->error, $error);
    }
}