<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait AxisX
{
    /**
     * @var array
     */
    protected $axisX = [];

    /**
     * @return array
     */
    public function getAxisX()
    {
        return $this->axisX;
    }

    /**
     * @param array $axisX
     *
     * @return $this
     */
    public function setAxisX(array $axisX)
    {
        $this->axisX = $axisX;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAxisXField(string $field, $value)
    {
        Helper::setArrayValue($this->axisX, $field, $value);

        return $this;
    }
}