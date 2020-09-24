<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoSourceFile extends Error
{
    /**
     * @const int
     */
    const CODE = 4953;

    /**
     * @var string
     */
    protected $tiny = 'No source file';
}