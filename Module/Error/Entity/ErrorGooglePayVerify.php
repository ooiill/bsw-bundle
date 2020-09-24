<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorGooglePayVerify extends Error
{
    /**
     * @const int
     */
    const CODE = 4944;

    /**
     * @var string
     */
    protected $tiny = 'Google pay failed verification';
}