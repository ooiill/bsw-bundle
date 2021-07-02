<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;

trait Theme
{
    /**
     * @var string
     */
    protected $theme = Abs::JS_CHART_THEME;

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getThemeName(): string
    {
        return Helper::cutString($this->getTheme(), ['/^-1', '.^0']);
    }

    /**
     * @param string $theme
     *
     * @return $this
     */
    public function setTheme(string $theme)
    {
        $this->theme = $theme;

        return $this;
    }
}