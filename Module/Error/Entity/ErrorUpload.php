<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorUpload extends Error
{
    /**
     * @const int
     */
    const CODE = 5102;

    /**
     * @var string
     */
    protected $tiny = 'Upload failed';
}