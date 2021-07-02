<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class Datetime extends Form
{
    use GetSetter\Size;
    use GetSetter\AllowClear;
    use GetSetter\Format;
    use GetSetter\ShowTime;
    use GetSetter\DisabledDate;
    use GetSetter\DisabledTime;

    /**
     * Datetime constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'date-picker';
    }
}