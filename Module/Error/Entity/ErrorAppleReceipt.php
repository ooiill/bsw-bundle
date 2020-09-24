<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAppleReceipt extends Error
{
    /**
     * @const int
     */
    const CODE = 4928;

    /**
     * @var string
     */
    protected $tiny = 'Illegal apple pay receipt';
}