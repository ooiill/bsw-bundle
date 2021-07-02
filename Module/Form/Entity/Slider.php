<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Form\Form;

class Slider extends Form
{
    use GetSetter\Dots;
    use GetSetter\Step;
    use GetSetter\Min;
    use GetSetter\Max;
    use GetSetter\Marks;
    use GetSetter\Included;
    use GetSetter\Range;
    use GetSetter\Vertical;
    use GetSetter\TooltipVisible;
    use GetSetter\TipFormatter;

    /**
     * Slider constructor.
     */
    public function __construct()
    {
        $this->setMax(100);
    }
}