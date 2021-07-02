<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait TreeData
{
    /**
     * @var array
     */
    protected $treeData = [];

    /**
     * @return string
     */
    public function getTreeData(): string
    {
        return Helper::jsonStringify($this->getTreeDataArray());
    }

    /**
     * @return array
     */
    public function getTreeDataArray(): array
    {
        return Helper::stringValues($this->treeData);
    }

    /**
     * @param array $treeData
     *
     * @return $this
     */
    public function setTreeData(array $treeData)
    {
        $this->treeData = $treeData;

        return $this;
    }
}