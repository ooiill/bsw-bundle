<?php

namespace Leon\BswBundle\Module\Error\Entity;

use Leon\BswBundle\Module\Error\Error;

class ErrorAttachment extends Error
{
    /**
     * @const int
     */
    const CODE = 4930;

    /**
     * @var string
     */
    protected $tiny = 'Attachment illegal';
}