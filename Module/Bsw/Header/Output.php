<?php

namespace Leon\BswBundle\Module\Bsw\Header;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Scene\Links;
use Leon\BswBundle\Module\Scene\Menu;
use Leon\BswBundle\Module\Scene\Setting;

class Output extends ArgsOutput
{
    /**
     * @var Menu[]
     */
    public $menu = [];

    /**
     * @var Setting[]
     */
    public $setting = [];

    /**
     * @var Links[]
     */
    public $links = [];

    /**
     * @var array
     */
    public $language = [];
}