<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorBetsSealed extends Error
{
    /**
     * @const int
     */
    const CODE = 4951;

    /**
     * @var string
     */
    protected $tiny = 'Bets already seal';
}