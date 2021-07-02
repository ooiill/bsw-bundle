<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Disabled
{
    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var bool
     */
    protected $alreadyDisabled = false;

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     * @param bool $coverAble
     *
     * @return $this
     */
    public function setDisabled(bool $disabled = true, bool $coverAble = false)
    {
        if ($this->alreadyDisabled) {
            if ($coverAble) {
                $this->disabled = $disabled;
            }
        } else {
            $this->disabled = $disabled;
        }

        $this->alreadyDisabled = true;

        return $this;
    }

    /**
     * @param bool $alreadyDisabled
     *
     * @return $this
     */
    public function setAlreadyDisabled(bool $alreadyDisabled = true)
    {
        $this->alreadyDisabled = $alreadyDisabled;

        return $this;
    }
}