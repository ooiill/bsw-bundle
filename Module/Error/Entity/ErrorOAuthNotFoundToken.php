<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOAuthNotFoundToken extends Error
{
    /**
     * @const int
     */
    const CODE = 4925;

    /**
     * @var string
     */
    protected $tiny = 'Invalid token';

    /**
     * @var string
     */
    protected $description = 'Token is not found';
}