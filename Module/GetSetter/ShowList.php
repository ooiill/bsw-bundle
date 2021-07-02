<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ShowList
{
    /**
     * @var bool
     */
    protected $showList = false;

    /**
     * @return bool
     */
    public function isShowList(): bool
    {
        return $this->showList;
    }

    /**
     * @param bool $showList
     *
     * @return $this
     */
    public function setShowList(bool $showList = true)
    {
        $this->showList = $showList;

        return $this;
    }
}