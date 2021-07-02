<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Checked
{
    /**
     * @var bool
     */
    protected $checked = false;

    /**
     * @return bool
     */
    public function isChecked(): bool
    {
        return $this->checked;
    }

    /**
     * @param bool $checked
     *
     * @return $this
     */
    public function setChecked(bool $checked)
    {
        $this->checked = $checked;

        return $this;
    }
}