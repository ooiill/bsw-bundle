<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorSamePassword extends Error
{
    /**
     * @const int
     */
    const CODE = 4916;

    /**
     * @var string
     */
    protected $tiny = 'Same password';
}