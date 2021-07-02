<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TipFormatter
{
    /**
     * @var string
     */
    protected $tipFormatter = '(value) => `${value}%`';

    /**
     * @return string
     */
    public function getTipFormatter(): string
    {
        return $this->tipFormatter;
    }

    /**
     * @param string $tipFormatter
     *
     * @return $this
     */
    public function setTipFormatter(string $tipFormatter)
    {
        $this->tipFormatter = $tipFormatter;

        return $this;
    }
}