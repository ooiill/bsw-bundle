<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait ShowSelectAll
{
    /**
     * @var bool
     */
    protected $showSelectAll = true;

    /**
     * @return bool
     */
    public function isShowSelectAll(): bool
    {
        return $this->showSelectAll;
    }

    /**
     * @param bool $showSelectAll
     *
     * @return $this
     */
    public function setShowSelectAll(bool $showSelectAll = true)
    {
        $this->showSelectAll = $showSelectAll;

        return $this;
    }
}