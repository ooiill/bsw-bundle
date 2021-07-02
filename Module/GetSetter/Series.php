<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Series
{
    /**
     * @var array
     */
    protected $series = [];

    /**
     * @return array
     */
    public function getSeries(): array
    {
        return $this->series;
    }

    /**
     * @param array $series
     *
     * @return $this
     */
    public function setSeries(array $series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSeriesField(string $field, $value)
    {
        Helper::setArrayValue($this->series, $field, $value);

        return $this;
    }
}