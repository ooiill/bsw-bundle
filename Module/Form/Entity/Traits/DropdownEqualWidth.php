<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

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