<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Module\Scene\Link;

class Setting
{
    use Link;

    /**
     * Setting constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $click
     */
    public function __construct(string $label = null, string $icon = null, string $click = null)
    {
        isset($label) && $this->label = $label;
        isset($icon) && $this->icon = $icon;
        isset($click) && $this->click = $click;
    }
}