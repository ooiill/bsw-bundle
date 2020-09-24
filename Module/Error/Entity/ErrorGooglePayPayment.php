<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorGooglePayPayment extends Error
{
    /**
     * @const int
     */
    const CODE = 4945;

    /**
     * @var string
     */
    protected $tiny = 'Google pay failed payment';
}