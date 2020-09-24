<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoCarousel extends Error
{
    /**
     * @const int
     */
    const CODE = 4947;

    /**
     * @var string
     */
    protected $tiny = 'No carousel';
}