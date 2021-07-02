<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait DataField
{
    protected $dataField = [];

    /**
     * @return array
     */
    public function getDataField(): array
    {
        return $this->dataField;
    }

    /**
     * @param array $dataField
     *
     * @return $this
     */
    public function setDataField(array $dataField)
    {
        $this->dataField = $dataField;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setDataFieldField(string $field, $value)
    {
        Helper::setArrayValue($this->dataField, $field, $value);

        return $this;
    }
}