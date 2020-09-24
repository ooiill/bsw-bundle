<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorParameter extends Error
{
    /**
     * @const int
     */
    const CODE = 4907;

    /**
     * @var string
     */
    protected $tiny = 'Nonstandard parameter';
}