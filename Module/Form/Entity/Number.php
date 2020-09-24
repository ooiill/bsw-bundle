<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\Max;
use Leon\BswBundle\Module\Form\Entity\Traits\Min;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\Step;
use Leon\BswBundle\Module\Form\Form;

class Number extends Form
{
    use Size;
    use PreviewRoute;
    use ButtonLabel;
    use Step;
    use Min;
    use Max;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setButtonLabel('Popup for select');
    }
}