<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorWxPay extends Error
{
    /**
     * @const int
     */
    const CODE = 4962;

    /**
     * @var string
     */
    protected $tiny = 'WeChat pay failed';
}