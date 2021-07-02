<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class Input extends Form
{
    use GetSetter\Size;
    use GetSetter\PreviewRoute;
    use GetSetter\AllowClear;
    use GetSetter\Type;
    use GetSetter\MaxLength;
    use GetSetter\Icon;
    use GetSetter\Prefix;
    use GetSetter\Suffix;

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