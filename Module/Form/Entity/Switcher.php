<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Form\Form;

class Switcher extends Form
{
    use GetSetter\Size;
    use GetSetter\CheckedChildren;
    use GetSetter\UnCheckedChildren;
}