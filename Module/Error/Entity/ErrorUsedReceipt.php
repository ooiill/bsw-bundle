<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorUsedReceipt extends Error
{
    /**
     * @const int
     */
    const CODE = 4938;

    /**
     * @var string
     */
    protected $tiny = 'Receipt is used';
}