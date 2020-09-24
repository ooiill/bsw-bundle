<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAreaCode extends Error
{
    /**
     * @const int
     */
    const CODE = 4929;

    /**
     * @var string
     */
    protected $tiny = 'Area code illegal';
}