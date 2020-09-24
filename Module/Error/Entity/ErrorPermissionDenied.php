<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorPermissionDenied extends Error
{
    /**
     * @const int
     */
    const CODE = 4900;

    /**
     * @var string
     */
    protected $tiny = 'Permission denied';
}