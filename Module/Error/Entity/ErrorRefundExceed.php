<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorRefundExceed extends Error
{
    /**
     * @const int
     */
    const CODE = 4965;

    /**
     * @var string
     */
    protected $tiny = 'Refund amount so much';
}