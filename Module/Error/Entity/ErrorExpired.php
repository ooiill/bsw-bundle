<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorExpired extends Error
{
    /**
     * @const int
     */
    const CODE = 4905;

    /**
     * @var string
     */
    protected $tiny = 'Expired';
}