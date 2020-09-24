<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var bool
     */
    public $fit = true;

    /**
     * @var string
     */
    public $size = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $type = Abs::TABS_TYPE_CARD;

    /**
     * @var int
     */
    public $tabBarGutter = 6;

    /**
     * @var string
     */
    public $position = Abs::POS_TOP;

    /**
     * @var string
     */
    public $clsName = 'bsw-align-center';
}