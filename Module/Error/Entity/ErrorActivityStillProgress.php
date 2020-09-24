<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorActivityStillProgress extends Error
{
    /**
     * @const int
     */
    const CODE = 4955;

    /**
     * @var string
     */
    protected $tiny = 'Activity still progress';
}