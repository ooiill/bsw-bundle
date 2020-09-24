<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\Separator;
use Leon\BswBundle\Module\Form\Entity\Traits\TimeBoundary;
use Leon\BswBundle\Module\Form\Entity\Traits\TimeFormat;

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