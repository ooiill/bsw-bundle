<?php

namespace Leon\BswBundle\Module\GetSetter;

trait UnCheckedChildren
{
    /**
     * @var string
     */
    protected $unCheckedChildren = 'Close';

    /**
     * @return string
     */
    public function getUnCheckedChildren(): string
    {
        return $this->unCheckedChildren;
    }

    /**
     * @param string $unCheckedChildren
     *
     * @return $this
     */
    public function setUnCheckedChildren(string $unCheckedChildren)
    {
        $this->unCheckedChildren = $unCheckedChildren;

        return $this;
    }
}