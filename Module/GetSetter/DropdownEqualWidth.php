<?php

namespace Leon\BswBundle\Module\GetSetter;

trait DropdownEqualWidth
{
    /**
     * @var bool
     */
    protected $dropdownEqualWidth = false;

    /**
     * @return bool
     */
    public function isDropdownEqualWidth(): bool
    {
        return $this->dropdownEqualWidth;
    }

    /**
     * @param bool $dropdownEqualWidth
     *
     * @return $this
     */
    public function setDropdownEqualWidth(bool $dropdownEqualWidth = true)
    {
        $this->dropdownEqualWidth = $dropdownEqualWidth;

        return $this;
    }
}