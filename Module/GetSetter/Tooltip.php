<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Tooltip
{
    /**
     * @var array
     */
    protected $tooltip = [
        'trigger'         => 'axis',
        'backgroundColor' => 'rgba(245, 245, 245, .8)',
        'borderWidth'     => 1,
        'borderColor'     => '#ccc',
        'padding'         => 10,
        'textStyle'       => [
            'color' => '#666',
        ],
        'axisPointer'     => [
            'type'        => 'shadow', // cross、shadow
            'label'       => [
                'backgroundColor' => 'rgba(150, 150, 150, .5)',
            ],
            'lineStyle'   => [
                'color' => 'rgba(150, 150, 150, .3)',
                'type'  => 'dashed',
            ],
            'crossStyle'  => [
                'color' => 'rgba(150, 150, 150, .3)',
                'type'  => 'dashed',
            ],
            'shadowStyle' => [
                'color' => 'rgba(150, 150, 150, .1)',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function getTooltip(): array
    {
        return $this->tooltip;
    }

    /**
     * @param array $tooltip
     *
     * @return $this
     */
    public function setTooltip(array $tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setTooltipField(string $field, $value)
    {
        Helper::setArrayValue($this->tooltip, $field, $value);

        return $this;
    }
}