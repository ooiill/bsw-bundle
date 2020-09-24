<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\MaxRows;
use Leon\BswBundle\Module\Form\Entity\Traits\MinRows;

class TextArea extends Input
{
    use MinRows;
    use MaxRows;
}