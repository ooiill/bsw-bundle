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
        if (is_null($this->key)) {
            return null;
        }
        if (strpos($this->key, '__') === 0) {
            return $this->key;
        }

        return Helper::underToCamel($this->key);
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