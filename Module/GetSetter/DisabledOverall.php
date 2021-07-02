<?php

namespace Leon\BswBundle\Module\GetSetter;

trait DisabledOverall
{
    /**
     * @var bool
     */
    protected $disabledOverall = true;

    /**
     * @return bool
     */
    public function isDisabledOverall(): bool
    {
        return $this->disabledOverall;
    }

    /**
     * @param bool $disabledOverall
     *
     * @return $this
     */
    public function setDisabledOverall(bool $disabledOverall = true)
    {
        $this->disabledOverall = $disabledOverall;

        return $this;
    }
}
