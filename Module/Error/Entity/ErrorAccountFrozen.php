<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAccountFrozen extends Error
{
    /**
     * @const int
     */
    const CODE = 4903;

    /**
     * @var string
     */
    protected $tiny = 'Invalid account';

    /**
     * @var string
     */
    protected $description = 'Accounts frozen';
}