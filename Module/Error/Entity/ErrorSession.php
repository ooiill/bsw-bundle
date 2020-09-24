<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorSession extends Error
{
    /**
     * @const int
     */
    const CODE = 4918;

    /**
     * @var string
     */
    protected $tiny = 'Invalid session';
}