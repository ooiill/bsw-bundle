<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAliPay extends Error
{
    /**
     * @const int
     */
    const CODE = 4963;

    /**
     * @var string
     */
    protected $tiny = 'Ali pay failed';
}