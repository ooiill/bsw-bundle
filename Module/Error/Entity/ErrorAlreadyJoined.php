<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAlreadyJoined extends Error
{
    /**
     * @const int
     */
    const CODE = 4954;

    /**
     * @var string
     */
    protected $tiny = 'Already joined';
}