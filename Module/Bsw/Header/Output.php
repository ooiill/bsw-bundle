<?php

namespace Leon\BswBundle\Module\Bsw\Header;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Bsw\Header\Entity\Setting;
use Leon\BswBundle\Module\Bsw\Menu\Entity\Menu;

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