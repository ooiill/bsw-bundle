<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Value
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (empty($this->value) || !is_string($this->value)) {
            return $this->value;
        }

        return str_replace(['`'], ['\`'], $this->value);
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}