<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;

class DateRange extends DatetimeRange
{
    /**
     * Date constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFormat('YYYY-MM-DD');
        $this->setShowTime(false);
    }
}