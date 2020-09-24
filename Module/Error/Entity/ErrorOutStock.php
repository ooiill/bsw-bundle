<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOutStock extends Error
{
    /**
     * @const int
     */
    const CODE = 4939;

    /**
     * @var string
     */
    protected $tiny = 'Out of stock';
}