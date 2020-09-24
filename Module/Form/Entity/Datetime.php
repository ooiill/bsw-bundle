<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\DisabledDate;
use Leon\BswBundle\Module\Form\Entity\Traits\DisabledTime;
use Leon\BswBundle\Module\Form\Entity\Traits\Format;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowTime;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Form;

class Datetime extends Form
{
    use Size;
    use AllowClear;
    use Format;
    use ShowTime;
    use DisabledDate;
    use DisabledTime;

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