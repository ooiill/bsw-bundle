<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Scene\Links;

class Output extends ArgsOutput
{
    /**
     * @var Links[]
     */
    public $links = [];

    /**
     * @var bool
     */
    public $fit;

    /**
     * @var string
     */
    public $size;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $tabBarGutter;

    /**
     * @var string
     */
    public $position;

    /**
     * @var string
     */
    public $clsName;
}