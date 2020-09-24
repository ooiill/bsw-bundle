<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNotThird extends Error
{
    /**
     * @const int
     */
    const CODE = 4946;

    /**
     * @var string
     */
    protected $tiny = 'Not third user';
}