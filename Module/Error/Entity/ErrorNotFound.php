<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNotFound extends Error
{
    /**
     * @const int
     */
    const CODE = 4972;

    /**
     * @var string
     */
    protected $tiny = 'Record not found';
}