<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorUA extends Error
{
    /**
     * @const int
     */
    const CODE = 4923;

    /**
     * @var string
     */
    protected $tiny = 'Device error';

    /**
     * @var string
     */
    protected $description = 'Device ua is required';
}