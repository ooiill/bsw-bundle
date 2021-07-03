<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Module\Scene\Link;

class Links
{
    use Link;

    /**
     * Links constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $route
     */
    public function __construct(string $label = null, string $route = null, string $icon = null)
    {
        isset($label) && $this->label = $label;
        isset($icon) && $this->icon = $icon;
        isset($route) && $this->route = $route;
    }
}