<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoPopup extends Error
{
    /**
     * @const int
     */
    const CODE = 4935;

    /**
     * @var string
     */
    protected $tiny = 'No popup';
}