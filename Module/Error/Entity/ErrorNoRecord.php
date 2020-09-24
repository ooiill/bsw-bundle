<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoRecord extends Error
{
    /**
     * @const int
     */
    const CODE = 4967;

    /**
     * @var string
     */
    protected $tiny = 'No record';
}