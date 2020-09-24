<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorMaxTimes extends Error
{
    /**
     * @const int
     */
    const CODE = 4942;

    /**
     * @var string
     */
    protected $tiny = 'Times upper limit';
}