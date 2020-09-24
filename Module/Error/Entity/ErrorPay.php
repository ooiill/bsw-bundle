<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorPay extends Error
{
    /**
     * @const int
     */
    const CODE = 4964;

    /**
     * @var string
     */
    protected $tiny = 'Pay action failed';
}