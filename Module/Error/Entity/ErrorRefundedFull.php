<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorRefundedFull extends Error
{
    /**
     * @const int
     */
    const CODE = 4966;

    /**
     * @var string
     */
    protected $tiny = 'Refunded in full';
}