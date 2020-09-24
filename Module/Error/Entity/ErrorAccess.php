<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAccess extends Error
{
    /**
     * @const int
     */
    const CODE = 4927;

    /**
     * @var string
     */
    protected $tiny = 'Access denied';
}