<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

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