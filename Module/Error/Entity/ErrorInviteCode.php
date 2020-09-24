<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorInviteCode extends Error
{
    /**
     * @const int
     */
    const CODE = 4920;

    /**
     * @var string
     */
    protected $tiny = 'Invalid invite code';
}