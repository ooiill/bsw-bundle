<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorPurchaseLimit extends Error
{
    /**
     * @const int
     */
    const CODE = 4948;

    /**
     * @var string
     */
    protected $tiny = 'Purchase upper limit';
}