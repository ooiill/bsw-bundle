<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNotSupported extends Error
{
    /**
     * @const int
     */
    const CODE = 4971;

    /**
     * @var string
     */
    protected $tiny = 'Not supported';
}