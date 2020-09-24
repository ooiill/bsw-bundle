<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorOldPassword extends Error
{
    /**
     * @const int
     */
    const CODE = 4917;

    /**
     * @var string
     */
    protected $tiny = 'Invalid old password';
}