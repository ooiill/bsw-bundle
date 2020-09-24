<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorMetaData extends Error
{
    /**
     * @const int
     */
    const CODE = 4943;

    /**
     * @var string
     */
    protected $tiny = 'Meta data illegal, feedback it';
}