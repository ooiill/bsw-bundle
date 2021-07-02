<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;

class Time extends Datetime
{
    use GetSetter\DisabledHour;
    use GetSetter\DisabledMinute;
    use GetSetter\DisabledSecond;
    use GetSetter\HourStep;
    use GetSetter\MinuteStep;
    use GetSetter\SecondStep;

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