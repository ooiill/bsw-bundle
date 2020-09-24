<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorProhibitedCountry extends Error
{
    /**
     * @const int
     */
    const CODE = 4961;

    /**
     * @var string
     */
    protected $tiny = 'Prohibited in current country';
}