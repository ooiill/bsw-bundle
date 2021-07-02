<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait Module
{
    /**
     * @var array
     */
    protected $module = [
        Abs::CHART_TITLE      => true,
        Abs::CHART_TOOLTIP    => true,
        Abs::CHART_TOOLBOX    => true,
        Abs::CHART_LEGEND     => true,
        Abs::CHART_GRID       => true,
        Abs::CHART_AXIS_X     => true,
        Abs::CHART_AXIS_Y     => true,
        Abs::CHART_ZOOM       => true,
        Abs::CHART_SERIES     => true,
        Abs::CHART_LINE       => true,
        Abs::CHART_POINT      => true,
        Abs::CHART_MAP_VISUAL => true,
        Abs::CHART_COLOR      => false,
    ];

    /**
     * @param string ...$names
     *
     * @return $this
     */
    public function moduleDisable(string ...$names)
    {
        foreach ($names as $name) {
            $this->module[$name] = false;
        }

        return $this;
    }

    /**
     * @param string ...$names
     *
     * @return $this
     */
    public function moduleEnable(string ...$names)
    {
        foreach ($names as $name) {
            $this->module[$name] = true;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function moduleState(string $name): bool
    {
        return $this->module[$name] ?? false;
    }
}