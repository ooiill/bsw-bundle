<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoAdvert extends Error
{
    /**
     * @const int
     */
    const CODE = 4931;

    /**
     * @var string
     */
    protected $tiny = 'No advert';
}