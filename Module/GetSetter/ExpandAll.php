<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ExpandAll
{
    /**
     * @var bool
     */
    protected $expandAll = false;

    /**
     * @return bool
     */
    public function isExpandAll(): bool
    {
        return $this->expandAll;
    }

    /**
     * @param bool $expandAll
     *
     * @return $this
     */
    public function setExpandAll(bool $expandAll = true)
    {
        $this->expandAll = $expandAll;

        return $this;
    }
}