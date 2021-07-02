<?php

namespace Leon\BswBundle\Module\GetSetter;

trait DisabledHour
{
    /**
     * @var string
     */
    protected $disabledHour;

    /**
     * @return string
     */
    public function getDisabledHour(): ?string
    {
        return $this->disabledHour;
    }

    /**
     * @param string $disabledHour
     *
     * @return $this
     */
    public function setDisabledHour(?string $disabledHour = null)
    {
        $this->disabledHour = $disabledHour;

        return $this;
    }
}