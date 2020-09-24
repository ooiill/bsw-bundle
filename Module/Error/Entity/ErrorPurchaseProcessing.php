<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorPurchaseProcessing extends Error
{
    /**
     * @const int
     */
    const CODE = 4949;

    /**
     * @var string
     */
    protected $tiny = 'Purchase processing';
}