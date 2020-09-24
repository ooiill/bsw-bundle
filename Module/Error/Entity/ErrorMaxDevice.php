<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorMaxDevice extends Error
{
    /**
     * @const int
     */
    const CODE = 4921;

    /**
     * @var string
     */
    protected $tiny = 'Device upper limit';
}