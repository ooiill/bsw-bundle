<?php

namespace Leon\BswBundle\Module\Bsw\Crumbs\Entity;

use Leon\BswBundle\Module\Traits\Link;

class Crumb
{
    use Link;

    /**
     * Crumbs constructor.
     *
     * @param string $label
     * @param string $route
     * @param string $icon
     */
    public function __construct(string $label = null, string $route = null, string $icon = null)
    {
        isset($label) && $this->label = $label;
        isset($route) && $this->route = $route;
        isset($icon) && $this->icon = $icon;
    }
}