<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SelectChange
{
    /**
     * @var string
     */
    protected $selectChange;

    /**
     * @return string
     */
    public function getSelectChange(): ?string
    {
        return $this->selectChange;
    }

    /**
     * @param string $selectChange
     *
     * @return $this
     */
    public function setSelectChange(string $selectChange = null)
    {
        $this->selectChange = $selectChange;

        return $this;
    }
}