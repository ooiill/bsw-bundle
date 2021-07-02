<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait SeriesExtra
{
    /**
     * @var array
     */
    protected $seriesExtra = [];

    /**
     * @return array
     */
    public function getSeriesExtra(): array
    {
        return $this->seriesExtra;
    }

    /**
     * @param array $seriesExtra
     *
     * @return $this
     */
    public function setSeriesExtra(array $seriesExtra)
    {
        $this->seriesExtra = $seriesExtra;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setSeriesExtraField(string $field, $value)
    {
        Helper::setArrayValue($this->seriesExtra, $field, $value);

        return $this;
    }
}