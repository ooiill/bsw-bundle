<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoBootstrap extends Error
{
    /**
     * @const int
     */
    const CODE = 4932;

    /**
     * @var string
     */
    protected $tiny = 'No bootstrap';
}