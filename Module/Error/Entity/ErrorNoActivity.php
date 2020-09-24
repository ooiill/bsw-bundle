<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoActivity extends Error
{
    /**
     * @const int
     */
    const CODE = 4941;

    /**
     * @var string
     */
    protected $tiny = 'Activity is not yet being';
}