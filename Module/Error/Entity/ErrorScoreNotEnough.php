<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorScoreNotEnough extends Error
{
    /**
     * @const int
     */
    const CODE = 4940;

    /**
     * @var string
     */
    protected $tiny = 'Score not enough';
}