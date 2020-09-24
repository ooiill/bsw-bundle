<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOAuthExpiredToken extends Error
{
    /**
     * @const int
     */
    const CODE = 4926;

    /**
     * @var string
     */
    protected $tiny = 'Invalid token';

    /**
     * @var string
     */
    protected $description = 'Token expired';
}