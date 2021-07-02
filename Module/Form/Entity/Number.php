<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class Number extends Form
{
    use GetSetter\Size;
    use GetSetter\PreviewRoute;
    use GetSetter\ButtonLabel;
    use GetSetter\Step;
    use GetSetter\Min;
    use GetSetter\Max;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setButtonLabel('Popup for select');
    }
}