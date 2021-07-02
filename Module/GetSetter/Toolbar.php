<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Toolbar
{
    /**
     * @var array
     */
    protected $toolbar = [];

    /**
     * @return array
     */
    public function getToolbar(): array
    {
        return $this->toolbar;
    }

    /**
     * @return string
     */
    public function getToolbarStringify(): string
    {
        return Helper::jsonStringify($this->getToolbar());
    }

    /**
     * @param array $toolbar
     *
     * @return $this
     */
    public function setToolbar(array $toolbar)
    {
        $this->toolbar = $toolbar;

        return $this;
    }
}