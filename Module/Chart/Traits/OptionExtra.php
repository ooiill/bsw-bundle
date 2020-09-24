<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Component\Helper;

trait OptionExtra
{
    /**
     * @var array
     */
    protected $optionExtra = [];

    /**
     * @return array
     */
    public function getOptionExtra(): array
    {
        return $this->optionExtra;
    }

    /**
     * @param array $optionExtra
     *
     * @return $this
     */
    public function setOptionExtra(array $optionExtra)
    {
        $this->optionExtra = $optionExtra;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOptionExtraField(string $field, $value)
    {
        Helper::setArrayValue($this->optionExtra, $field, $value);

        return $this;
    }
}