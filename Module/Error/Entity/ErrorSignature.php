<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorSignature extends Error
{
    /**
     * @const int
     */
    const CODE = 4909;

    /**
     * @var string
     */
    protected $tiny = 'Error signature';
}