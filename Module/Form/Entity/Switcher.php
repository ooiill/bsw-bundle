<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\CheckedChildren;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\UnCheckedChildren;
use Leon\BswBundle\Module\Form\Form;

class Switcher extends Form
{
    use Size;
    use CheckedChildren;
    use UnCheckedChildren;
}