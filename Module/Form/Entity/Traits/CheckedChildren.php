<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait CheckedChildren
{
    /**
     * @var string
     */
    protected $checkedChildren = 'Open';

    /**
     * @return string
     */
    public function getCheckedChildren(): string
    {
        return $this->checkedChildren;
    }

    /**
     * @param string $checkedChildren
     *
     * @return $this
     */
    public function setCheckedChildren(string $checkedChildren)
    {
        $this->checkedChildren = $checkedChildren;

        return $this;
    }
}