<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorCaptcha extends Error
{
    /**
     * @const int
     */
    const CODE = 4901;

    /**
     * @var string
     */
    protected $tiny = 'Error captcha';
}