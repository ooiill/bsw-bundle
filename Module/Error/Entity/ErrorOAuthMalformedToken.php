<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOAuthMalformedToken extends Error
{
    /**
     * @const int
     */
    const CODE = 4924;

    /**
     * @var string
     */
    protected $tiny = 'Malformed token';

    /**
     * @var string
     */
    protected $description = 'Invalid client id or others';
}