<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait SelectedKeys
{
    /**
     * @var array
     */
    protected $selectedKeys = [];

    /**
     * @return string
     */
    public function getSelectedKeys(): string
    {
        return Helper::jsonStringify($this->selectedKeys);
    }

    /**
     * @return array
     */
    public function getSelectedKeysArray(): array
    {
        return $this->selectedKeys;
    }

    /**
     * @param array $selectedKeys
     *
     * @return $this
     */
    public function setSelectedKeys(array $selectedKeys)
    {
        $this->selectedKeys = $selectedKeys;

        return $this;
    }
}