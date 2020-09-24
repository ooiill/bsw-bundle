<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorSns extends Error
{
    /**
     * @const int
     */
    const CODE = 4906;

    /**
     * @var string
     */
    protected $tiny = 'Exceptions sns';
}