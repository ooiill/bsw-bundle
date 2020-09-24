<?php

namespace Leon\BswBundle\Module\Chart\Traits;

use Leon\BswBundle\Component\Helper;

trait Grid
{
    /**
     * @var array
     */
    protected $grid = [];

    /**
     * @return array
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * @param array $grid
     *
     * @return $this
     */
    public function setGrid(array $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setGridField(string $field, $value)
    {
        Helper::setArrayValue($this->grid, $field, $value);

        return $this;
    }
}