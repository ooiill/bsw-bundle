<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait LegendTitle
{
    /**
     * @var array
     */
    protected $legendTitle = [];

    /**
     * @return array
     */
    public function getLegendTitle(): array
    {
        return $this->legendTitle;
    }

    /**
     * @param array $legendTitle
     *
     * @return $this
     */
    public function setLegendTitle(array $legendTitle)
    {
        $this->legendTitle = $legendTitle;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setLegendTitleField(string $field, $value)
    {
        Helper::setArrayValue($this->legendTitle, $field, $value);

        return $this;
    }
}