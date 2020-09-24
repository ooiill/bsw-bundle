<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorNoNewVersion extends Error
{
    /**
     * @const int
     */
    const CODE = 4933;

    /**
     * @var string
     */
    protected $tiny = 'No new version';
}