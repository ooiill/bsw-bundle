<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorTimeout extends Error
{
    /**
     * @const int
     */
    const CODE = 5101;

    /**
     * @var string
     */
    protected $tiny = 'Execution timeout';
}