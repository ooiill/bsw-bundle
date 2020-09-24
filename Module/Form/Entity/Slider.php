<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Dots;
use Leon\BswBundle\Module\Form\Entity\Traits\Included;
use Leon\BswBundle\Module\Form\Entity\Traits\Marks;
use Leon\BswBundle\Module\Form\Entity\Traits\Max;
use Leon\BswBundle\Module\Form\Entity\Traits\Min;
use Leon\BswBundle\Module\Form\Entity\Traits\Range;
use Leon\BswBundle\Module\Form\Entity\Traits\Step;
use Leon\BswBundle\Module\Form\Entity\Traits\TipFormatter;
use Leon\BswBundle\Module\Form\Entity\Traits\TooltipVisible;
use Leon\BswBundle\Module\Form\Entity\Traits\Vertical;
use Leon\BswBundle\Module\Form\Form;

class Slider extends Form
{
    use Dots;
    use Step;
    use Min;
    use Max;
    use Marks;
    use Included;
    use Range;
    use Vertical;
    use TooltipVisible;
    use TipFormatter;

    /**
     * Slider constructor.
     */
    public function __construct()
    {
        $this->setMax(100);
    }
}