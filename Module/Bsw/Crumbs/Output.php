<?php

namespace Leon\BswBundle\Module\Bsw\Crumbs;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Crumbs\Entity\Crumb;

class Output extends ArgsOutput
{
    /**
     * @var Crumb[]
     */
    public $list = [];
}