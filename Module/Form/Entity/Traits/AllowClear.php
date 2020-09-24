<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait AllowClear
{
    /**
     * @var bool
     */
    protected $allowClear = true;

    /**
     * @return bool
     */
    public function isAllowClear(): bool
    {
        return $this->allowClear;
    }

    /**
     * @param bool $allowClear
     *
     * @return $this
     */
    public function setAllowClear(bool $allowClear = true)
    {
        $this->allowClear = $allowClear;

        return $this;
    }
}