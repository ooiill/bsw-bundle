<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ShowSearch
{
    /**
     * @var bool
     */
    protected $showSearch = true;

    /**
     * @return bool
     */
    public function isShowSearch(): bool
    {
        return $this->showSearch;
    }

    /**
     * @param bool $showSearch
     *
     * @return $this
     */
    public function setShowSearch(bool $showSearch = true)
    {
        $this->showSearch = $showSearch;

        return $this;
    }
}