<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\Chart\Chart;
use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;

class Line extends Chart
{
    use GetSetter\Smooth,
        GetSetter\Point,
        GetSetter\Line;

    /**
     * @var string
     */
    protected $type = 'line';

    /**
     * @var array
     */
    protected $axisX = [
        'axisLine'    => [
            'lineStyle' => [
                'color' => '#666',
            ],
        ],
        'boundaryGap' => true,
        'axisLabel'   => [
            'rotate' => 20,
            'margin' => 15,
        ],
    ];

    /**
     * @var array
     */
    protected $axisY = [
        'axisLine' => [
            'lineStyle' => [
                'color' => '#666',
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $toolbox = [
        'feature' => [
            'dataZoom'  => [
                'yAxisIndex' => 'none',
                'title'      => [
                    'zoom' => 'Zoom',
                    'back' => 'Reset',
                ],
            ],
            'magicType' => [
                'type'  => ['line', 'bar'],
                'title' => [
                    'line' => 'Line',
                    'bar'  => 'Bar',
                ],
            ],
            'restore'   => [
                'title' => 'Reset',
            ],
        ],
    ];

    /**
     * @inheritdoc
     * @return void
     */
    protected function init()
    {
        // stack tooltip tpl
        if ($seriesExtra = current($this->getSeriesExtra())) {
            if (isset($seriesExtra['stack'])) {
                $this->setTooltipTpl('fn:TooltipStack');
            }
        }

        // reverse the point color
        $reverse = $this->isPointSenseReverse();
        $this->setPointField('max.itemStyle.color', $reverse ? $this->getPointBad() : $this->getPointGood());
        $this->setPointField('min.itemStyle.color', $reverse ? $this->getPointGood() : $this->getPointBad());

        $this->setAxisXTitle($this->getDataField())
            ->setLegendTitle(array_keys($this->getDataList()))
            ->setTooltipField('formatter', $this->getTooltipTpl())
            ->setPoint(array_values($this->getPoint()))
            ->setLine(array_values($this->getLine()));

        foreach ($this->getLegendTitle() as $key => $val) {
            $this->setLegendTitleField($key, strval($val));
        }
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param array  $item
     *
     * @return array
     */
    protected function buildSeries(string $name, array $item): array
    {
        return [
            'lineStyle'  => [
                'normal' => [
                    'width'         => 1.2,
                    'shadowColor'   => 'rgba(0, 0, 0, .4)',
                    'shadowBlur'    => 4,
                    'shadowOffsetY' => 4,
                ],
            ],
            'smooth'     => $this->isSmooth(),
            'symbolSize' => 6,
            'markPoint'  => [
                'data' => $this->moduleState(Abs::CHART_POINT) ? $this->getPoint() : null,
            ],
            'markLine'   => [
                'data'     => $this->moduleState(Abs::CHART_LINE) ? $this->getLine() : null,
                'symbol'   => ['none', 'none'],
                'label'    => [
                    'position' => 'insideStartTop',
                ],
                'emphasis' => [
                    'lineStyle' => [
                        'width' => 1,
                    ],
                ],
            ],
        ];
    }
}