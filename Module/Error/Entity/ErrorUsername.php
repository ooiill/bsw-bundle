<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorUsername extends Error
{
    /**
     * @const int
     */
    const CODE = 4904;

    /**
     * @var string
     */
    protected $tiny = 'Invalid account';

    /**
     * @var string
     */
    protected $description = 'Error username';
}