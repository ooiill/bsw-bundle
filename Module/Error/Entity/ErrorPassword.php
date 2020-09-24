<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorPassword extends Error
{
    /**
     * @const int
     */
    const CODE = 4911;

    /**
     * @var string
     */
    protected $tiny = 'Invalid account';

    /**
     * @var string
     */
    protected $description = 'Error password';
}