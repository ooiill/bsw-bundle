<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TimeFormat
{
    /**
     * @var string
     */
    protected $timeFormat = 'HH:mm:ss';

    /**
     * @return string
     */
    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    /**
     * @param string $timeFormat
     *
     * @return $this
     */
    public function setTimeFormat(string $timeFormat)
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }
}