<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNotExists extends Error
{
    /**
     * @const int
     */
    const CODE = 4973;

    /**
     * @var string
     */
    protected $tiny = 'File not exists';
}