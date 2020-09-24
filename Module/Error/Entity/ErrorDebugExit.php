<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorDebugExit extends Error
{
    /**
     * @const int
     */
    const CODE = 1100;

    /**
     * @var string
     */
    protected $tiny = 'Exit status debug';
}