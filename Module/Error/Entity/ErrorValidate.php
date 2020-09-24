<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorValidate extends Error
{
    /**
     * @const int
     */
    const CODE = 4913;

    /**
     * @var string
     */
    protected $tiny = 'Validate failed';
}