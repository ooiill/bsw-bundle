<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Component\Helper;

trait Key
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @return string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }
}