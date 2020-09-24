<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\DisabledHour;
use Leon\BswBundle\Module\Form\Entity\Traits\DisabledMinute;
use Leon\BswBundle\Module\Form\Entity\Traits\DisabledSecond;
use Leon\BswBundle\Module\Form\Entity\Traits\HourStep;
use Leon\BswBundle\Module\Form\Entity\Traits\MinuteStep;
use Leon\BswBundle\Module\Form\Entity\Traits\SecondStep;

class Time extends Datetime
{
    use DisabledHour;
    use DisabledMinute;
    use DisabledSecond;
    use HourStep;
    use MinuteStep;
    use SecondStep;

    /**
     * Time constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFormat('HH:mm:ss');
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'time-picker';
    }
}