<?php

namespace Leon\BswBundle\Module\GetSetter;

trait ValueShadow
{
    /**
     * @var mixed
     */
    protected $valueShadow;

    /**
     * @return mixed
     */
    public function getValueShadow()
    {
        if (empty($this->valueShadow) || !is_string($this->valueShadow)) {
            return $this->valueShadow;
        }

        return str_replace(['`'], ['\`'], $this->valueShadow);
    }

    /**
     * @param mixed $valueShadow
     *
     * @return $this
     */
    public function setValueShadow($valueShadow)
    {
        $this->valueShadow = $valueShadow;

        return $this;
    }
}
