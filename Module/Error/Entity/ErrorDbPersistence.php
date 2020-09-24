<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorDbPersistence extends Error
{
    /**
     * @const int
     */
    const CODE = 4908;

    /**
     * @var string
     */
    protected $tiny = 'Invalid data';

    /**
     * @var string
     */
    protected $description = 'Data persistence failed';
}