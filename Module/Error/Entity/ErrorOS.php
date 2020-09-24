<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOS extends Error
{
    /**
     * @const int
     */
    const CODE = 4922;

    /**
     * @var string
     */
    protected $tiny = 'Device error';

    /**
     * @var string
     */
    protected $description = 'Device os is required';
}