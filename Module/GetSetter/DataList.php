<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait DataList
{
    /**
     * @var array
     */
    protected $dataList = [];

    /**
     * @return array
     */
    public function getDataList(): array
    {
        return $this->dataList;
    }

    /**
     * @param array $dataList
     *
     * @return $this
     */
    public function setDataList(array $dataList)
    {
        $this->dataList = $dataList;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setDataListField(string $field, $value)
    {
        Helper::setArrayValue($this->dataList, $field, $value);

        return $this;
    }
}