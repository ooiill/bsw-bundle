<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait TargetKeys
{
    /**
     * @var array
     */
    protected $targetKeys = [];

    /**
     * @return string
     */
    public function getTargetKeys(): string
    {
        return Helper::jsonStringify($this->targetKeys);
    }

    /**
     * @return array
     */
    public function getTargetKeysArray(): array
    {
        return $this->targetKeys;
    }

    /**
     * @param array $targetKeys
     *
     * @return $this
     */
    public function setTargetKeys(array $targetKeys)
    {
        $this->targetKeys = $targetKeys;

        return $this;
    }
}