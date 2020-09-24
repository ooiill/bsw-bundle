<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\Icon;
use Leon\BswBundle\Module\Form\Entity\Traits\MaxLength;
use Leon\BswBundle\Module\Form\Entity\Traits\Prefix;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\Suffix;
use Leon\BswBundle\Module\Form\Entity\Traits\Type;
use Leon\BswBundle\Module\Form\Form;

class Input extends Form
{
    use Size;
    use PreviewRoute;
    use AllowClear;
    use Type;
    use MaxLength;
    use Icon;
    use Prefix;
    use Suffix;

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setAllowClear(false);
        $this->setType(Abs::TYPE_TEXT);
    }
}