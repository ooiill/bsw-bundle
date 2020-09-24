<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorTouristAlreadyBind extends Error
{
    /**
     * @const int
     */
    const CODE = 4952;

    /**
     * @var string
     */
    protected $tiny = 'Tourist already bind';
}