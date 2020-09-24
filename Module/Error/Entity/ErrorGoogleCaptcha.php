<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorGoogleCaptcha extends Error
{
    /**
     * @const int
     */
    const CODE = 4956;

    /**
     * @var string
     */
    protected $tiny = 'Error google captcha';
}