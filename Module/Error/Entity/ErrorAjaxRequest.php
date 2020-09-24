<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAjaxRequest extends Error
{
    /**
     * @const int
     */
    const CODE = 4915;

    /**
     * @var string
     */
    protected $tiny = 'Must be ajax request';
}