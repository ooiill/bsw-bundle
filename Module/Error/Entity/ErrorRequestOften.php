<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorRequestOften extends Error
{
    /**
     * @const int
     */
    const CODE = 4914;

    /**
     * @var string
     */
    protected $tiny = 'Request often';
}