<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Separator
{
    /**
     * @var string
     */
    protected $separator = '~';

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;

        return $this;
    }
}