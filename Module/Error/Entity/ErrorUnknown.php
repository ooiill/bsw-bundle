<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorUnknown extends Error
{
    /**
     * @const int
     */
    const CODE = 4910;

    /**
     * @var string
     */
    protected $tiny = 'Unknown error';
}