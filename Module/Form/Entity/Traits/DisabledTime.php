<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait DisabledTime
{
    /**
     * @var string
     */
    protected $disabledTime;

    /**
     * @return string
     */
    public function getDisabledTime(): ?string
    {
        return $this->disabledTime;
    }

    /**
     * @param string $disabledTime
     *
     * @return $this
     */
    public function setDisabledTime(?string $disabledTime = null)
    {
        $this->disabledTime = $disabledTime;

        return $this;
    }
}