<?php

namespace Leon\BswBundle\Module\GetSetter;

trait DisabledDate
{
    /**
     * @var string
     */
    protected $disabledDate;

    /**
     * @return string
     */
    public function getDisabledDate(): ?string
    {
        return $this->disabledDate;
    }

    /**
     * @param string $disabledDate
     *
     * @return $this
     */
    public function setDisabledDate(?string $disabledDate = null)
    {
        $this->disabledDate = $disabledDate;

        return $this;
    }
}