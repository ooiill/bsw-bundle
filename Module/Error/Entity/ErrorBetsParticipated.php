<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorBetsParticipated extends Error
{
    /**
     * @const int
     */
    const CODE = 4950;

    /**
     * @var string
     */
    protected $tiny = 'Bets already participate';
}