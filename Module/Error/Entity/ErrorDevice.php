<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorDevice extends Error
{
    /**
     * @const int
     */
    const CODE = 4919;

    /**
     * @var string
     */
    protected $tiny = 'Device error';

    /**
     * @var string
     */
    protected $description = 'Device id is required';
}