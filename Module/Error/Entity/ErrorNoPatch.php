<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoPatch extends Error
{
    /**
     * @const int
     */
    const CODE = 4934;

    /**
     * @var string
     */
    protected $tiny = 'No patch';
}