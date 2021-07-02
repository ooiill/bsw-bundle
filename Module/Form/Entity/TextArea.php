<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;

class TextArea extends Input
{
    use GetSetter\MinRows;
    use GetSetter\MaxRows;
}