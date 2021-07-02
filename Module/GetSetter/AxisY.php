<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait AxisY
{
    /**
     * @var array
     */
    protected $axisY = [];

    /**
     * @return array
     */
    public function getAxisY()
    {
        return $this->axisY;
    }

    /**
     * @param array $axisY
     *
     * @return $this
     */
    public function setAxisY(array $axisY)
    {
        $this->axisY = $axisY;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAxisYField(string $field, $value)
    {
        Helper::setArrayValue($this->axisY, $field, $value);

        return $this;
    }
}