<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Accept
{
    /**
     * @var string
     */
    protected $accept = '*';

    /**
     * @return string
     */
    public function getAccept(): string
    {
        return $this->accept;
    }

    /**
     * @param string $accept
     *
     * @return $this
     */
    public function setAccept(string $accept)
    {
        $this->accept = $accept;

        return $this;
    }
}