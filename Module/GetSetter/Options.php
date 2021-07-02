<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Options
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return string
     */
    public function getOptions(): string
    {
        return Helper::jsonStringify($this->getOptionsArray());
    }

    /**
     * @return array
     */
    public function getOptionsArray(): array
    {
        return Helper::stringValues($this->options);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function appendOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }
}