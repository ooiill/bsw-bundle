<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait DisabledSecond
{
    /**
     * @var string
     */
    protected $disabledSecond;

    /**
     * @return string
     */
    public function getDisabledSecond(): ?string
    {
        return $this->disabledSecond;
    }

    /**
     * @param string $disabledSecond
     *
     * @return $this
     */
    public function setDisabledSecond(?string $disabledSecond = null)
    {
        $this->disabledSecond = $disabledSecond;

        return $this;
    }
}