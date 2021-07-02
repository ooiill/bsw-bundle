<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Selected
{
    /**
     * @var array
     */
    protected $selected = [];

    /**
     * @return array
     */
    public function getSelected(): array
    {
        return $this->selected;
    }

    /**
     * @param array $selected
     *
     * @return $this
     */
    public function setSelected(array $selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSelectedField(string $field, $value)
    {
        Helper::setArrayValue($this->selected, $field, $value);

        return $this;
    }
}