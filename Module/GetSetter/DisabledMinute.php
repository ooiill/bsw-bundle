<?php

namespace Leon\BswBundle\Module\GetSetter;

trait DisabledMinute
{
    /**
     * @var string
     */
    protected $disabledMinute;

    /**
     * @return string
     */
    public function getDisabledMinute(): ?string
    {
        return $this->disabledMinute;
    }

    /**
     * @param string $disabledMinute
     *
     * @return $this
     */
    public function setDisabledMinute(?string $disabledMinute = null)
    {
        $this->disabledMinute = $disabledMinute;

        return $this;
    }
}