<?php

namespace Leon\BswBundle\Module\Bsw\Operate;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Choice;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var Button[]
     */
    public $buttons = [];

    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var string
     */
    public $position;

    /**
     * @var string
     */
    public $clsName;
}