<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorWithoutChange extends Error
{
    /**
     * @const int
     */
    const CODE = 4968;

    /**
     * @var string
     */
    protected $tiny = 'Without change';
}