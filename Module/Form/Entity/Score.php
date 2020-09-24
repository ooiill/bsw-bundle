<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowHalf;
use Leon\BswBundle\Module\Form\Entity\Traits\Character;
use Leon\BswBundle\Module\Form\Entity\Traits\Count;
use Leon\BswBundle\Module\Form\Form;

class Score extends Form
{
    use AllowClear;
    use AllowHalf;
    use Character;
    use Count;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setAllowClear(false);
    }
}