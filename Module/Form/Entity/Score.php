<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Form\Form;

class Score extends Form
{
    use GetSetter\AllowClear;
    use GetSetter\AllowHalf;
    use GetSetter\Character;
    use GetSetter\Count;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setAllowClear(false);
    }
}