<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNotTourist extends Error
{
    /**
     * @const int
     */
    const CODE = 4936;

    /**
     * @var string
     */
    protected $tiny = 'Not tourist';
}