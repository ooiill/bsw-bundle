<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorScript extends Error
{
    /**
     * @const int
     */
    const CODE = 1101;

    /**
     * @var string
     */
    protected $tiny = 'Script exception';
}