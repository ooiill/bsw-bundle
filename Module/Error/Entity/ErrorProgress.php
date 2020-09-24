<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorProgress extends Error
{
    /**
     * @const int
     */
    const CODE = 4970;

    /**
     * @var string
     */
    protected $tiny = 'Progress must enlarge';
}