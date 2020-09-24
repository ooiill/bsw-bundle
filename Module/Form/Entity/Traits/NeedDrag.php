<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait NeedDrag
{
    /**
     * @var bool
     */
    protected $needDrag = true;

    /**
     * @return bool
     */
    public function isNeedDrag(): bool
    {
        return $this->needDrag;
    }

    /**
     * @param bool $needDrag
     *
     * @return $this
     */
    public function setNeedDrag(bool $needDrag = true)
    {
        $this->needDrag = $needDrag;

        return $this;
    }
}