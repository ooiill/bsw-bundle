<?php

namespace Leon\BswBundle\Module\Chart;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;

abstract class Chart
{
    use GetSetter\Api,
        GetSetter\AxisX,
        GetSetter\AxisXTitle,
        GetSetter\AxisY,
        GetSetter\BackgroundColor,
        GetSetter\Color,
        GetSetter\DataField,
        GetSetter\DataFieldKey,
        GetSetter\DataList,
        GetSetter\Grid,
        GetSetter\Height,
        GetSetter\Legend,
        GetSetter\LegendTitle,
        GetSetter\MaxZoom,
        GetSetter\Mobile,
        GetSetter\Module,
        GetSetter\ChartName,
        GetSetter\Option,
        GetSetter\OptionExtra,
        GetSetter\SaveName,
        GetSetter\Selected,
        GetSetter\SelectedMode,
        GetSetter\Series,
        GetSetter\SeriesExtra,
        GetSetter\ChartStyle,
        GetSetter\SubTitleText,
        GetSetter\SubTitleLink,
        GetSetter\Theme,
        GetSetter\Title,
        GetSetter\TitleText,
        GetSetter\TitleLink,
        GetSetter\Toolbox,
        GetSetter\Tooltip,
        GetSetter\TooltipTpl,
        GetSetter\TooltipPosition,
        GetSetter\Type,
        GetSetter\Width;

    /**
     * Chart constructor.
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->setMobile(Helper::isMobile());
        $this->setSelectedMode(Abs::SELECTOR_MODE_MULTIPLE);

        if ($name) {
            $this->setName($name);
        }
    }

    /**
     * Rebuild data and field
     */
    protected function rebuildDataAndField()
    {
        $data = $this->getDataList();
        foreach ($data as $key => &$item) {
            if (!$this->getDataField()) {
                if ($key = $this->getDataFieldKey()) {
                    $this->setDataField(array_column($item, $key));
                } else {
                    $this->setDataField(array_keys($item));
                }
            }
            $item = array_values($item);
        }
        $this->setDataList($data);
    }

    /**
     * Init
     *
     * @return mixed
     */
    abstract protected function init();

    /**
     * Build option
     *
     * @return $this
     */
    final public function buildOption()
    {
        $this->rebuildDataAndField();
        $this->init();
        $titleNumber = (($this->getTitleText() ? 1 : 0) + ($this->getSubTitleText() ? 1 : 0));

        // title
        if ($this->moduleState(Abs::CHART_TITLE)) {
            $option['title'] = Helper::merge(
                [
                    'text'      => $this->getTitleText(),
                    'link'      => $this->getTitleLink(),
                    'textStyle' => [
                        'fontSize'   => 14,
                        'fontWeight' => 'lighter',
                    ],
                    'subtext'   => $this->getSubTitleText(),
                    'sublink'   => $this->getSubTitleLink(),
                    'x'         => 'center',
                    'itemGap'   => 8,
                    'bottom'    => 8,
                ],
                $this->getTitle()
            );
        }

        // tooltip
        if ($this->moduleState(Abs::CHART_TOOLTIP)) {
            $this->setTooltipField('position', $this->getTooltipPosition());
            $option['tooltip'] = $this->getTooltip();
        }

        // legend
        if ($this->moduleState(Abs::CHART_LEGEND)) {
            $option['legend'] = Helper::merge(
                [
                    'data'         => $this->getLegendTitle(),
                    'selectedMode' => $this->getSelectedMode(),
                    'selected'     => $this->getSelected(),
                    'type'         => 'scroll',
                    'align'        => 'auto',
                    'top'          => 15,
                    'width'        => '90%',
                ],
                $this->getLegend()
            );
        }

        // toolbox
        if ($this->moduleState(Abs::CHART_TOOLBOX) && !$this->isMobile()) {
            $option['toolbox'] = Helper::merge(
                [
                    'orient'   => 'vertical',
                    'top'      => 60 - 5,
                    'right'    => 10,
                    'itemSize' => 10,
                    'feature'  => [
                        'saveAsImage' => [
                            'title'      => 'Download',
                            'pixelRatio' => 2,
                            'name'       => $this->getSaveName() ?: $this->getTitleText(),
                        ],
                    ],
                ],
                $this->getToolbox()
            );
        }

        // grid
        if ($this->moduleState(Abs::CHART_GRID)) {
            $titleNumberMapToBottom = [
                0 => 15,
                1 => 45,
                2 => 65,
            ];
            $this->setGridField('bottom', $titleNumberMapToBottom[$titleNumber]);
            $option['grid'] = Helper::merge(
                [
                    'top'          => 60,
                    'right'        => empty($option['toolbox']) ? 15 : 45,
                    'left'         => $this->isMobile() ? 0 : 15,
                    'containLabel' => true,
                ],
                $this->getGrid()
            );
        }

        // x-axis
        if ($this->moduleState(Abs::CHART_AXIS_X)) {
            $this->setAxisXField('data', $this->getAxisXTitle());
            $option['xAxis'] = $this->getAxisX();
        }

        // y-axis
        if ($this->moduleState(Abs::CHART_AXIS_Y)) {
            $option['yAxis'] = $this->getAxisY();
        }

        // zoom
        if ($this->moduleState(Abs::CHART_ZOOM)) {

            $maxZoom = $this->getMaxZoom();
            $totalXAxis = count($this->getDataField());

            if ($maxZoom && ($totalXAxis > $maxZoom)) {
                $percent = intval(($maxZoom / $totalXAxis) * 100);
                $percent = $percent > 100 ? 100 : $percent;
                $option['dataZoom'] = [
                    [
                        'type'     => 'inside',
                        'start'    => 100 - $percent,
                        'end'      => 100,
                        'zoomLock' => true,
                    ],
                    [
                        'type'  => 'slider',
                        'start' => 100 - $percent,
                        'end'   => 100,
                    ],
                ];
            }
        }

        // series
        if ($this->moduleState(Abs::CHART_SERIES)) {
            $series = $this->getSeries();
            $seriesExtra = $this->getSeriesExtra();

            foreach ($this->getDataList() as $name => $item) {
                $buildSeries = $this->buildSeries($name, $item);
                $defaultSeries = [
                    'name' => $name,
                    'data' => $item,
                    'type' => $this->getType(),
                ];

                $buildSeries = Helper::merge($defaultSeries, $buildSeries, $seriesExtra[$name] ?? []);
                $buildSeries = array_filter(
                    $buildSeries,
                    function ($v) {
                        return !is_null($v);
                    }
                );
                array_push($series, $this->rebuildSeries($buildSeries, $item));
            }

            $option['series'] = $series;
        }

        // color
        if ($this->moduleState(Abs::CHART_COLOR)) {
            $option['color'] = $this->getColor();
        }

        // background color
        $option['backgroundColor'] = $this->getBackgroundColor();

        $option = $this->rebuildOption($option ?? []);
        $option = Helper::merge($option, $this->getOptionExtra());

        return $this->setOption($option);
    }

    /**
     * Rebuild option
     *
     * @param array $option
     *
     * @return array
     */
    protected function rebuildOption(array $option): array
    {
        return $option;
    }

    /**
     * Build series
     *
     * @param string $name
     * @param array  $item
     *
     * @return array
     */
    abstract protected function buildSeries(string $name, array $item): array;

    /**
     * Rebuild series
     *
     * @param array $series
     * @param array $item
     *
     * @return array
     */
    protected function rebuildSeries(array $series, array $item): array
    {
        return $series;
    }
}