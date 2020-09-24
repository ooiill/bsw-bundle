<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOAuthInvalidToken extends Error
{
    /**
     * @const int
     */
    const CODE = 4969;

    /**
     * @var string
     */
    protected $tiny = 'Invalid token';

    /**
     * @var string
     */
    protected $description = 'Invalid token';
}