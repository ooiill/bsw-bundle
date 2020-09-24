<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorPayMethod extends Error
{
    /**
     * @const int
     */
    const CODE = 4937;

    /**
     * @var string
     */
    protected $tiny = 'Pay method not supported';
}