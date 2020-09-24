<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Prefix
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @return string
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}