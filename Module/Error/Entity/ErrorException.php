<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorException extends Error
{
    /**
     * @const int
     */
    const CODE = 5100;

    /**
     * @var string
     */
    protected $tiny = 'Function exception';
}