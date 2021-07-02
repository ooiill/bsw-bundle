<?php

namespace Leon\BswBundle\Module\Chart\Entity;

use Leon\BswBundle\Module\GetSetter;

class Bar extends Line
{
    use GetSetter\LabelStackTpl,
        GetSetter\MaxBarFixedWidth;

    /**
     * @var string
     */
    protected $type = 'bar';

    /**
     * @inheritdoc
     *
     * @param array $series
     * @param array $item
     *
     * @return array
     */
    protected function rebuildSeries(array $series, array $item): array
    {
        if ($this->isMobile() || (count($item) > $this->getMaxBarFixedWidth())) {
            unset($series['barWidth']);
        }

        if (!empty($series['stack'])) {
            $series['label'] = [
                'normal' => [
                    'show'          => !$this->isMobile(),
                    'position'      => 'insideBottom',
                    'distance'      => 15,
                    'align'         => 'center',
                    'verticalAlign' => 'middle',
                    'rotate'        => 0,
                    'formatter'     => $this->getLabelStackTpl(),
                    'fontSize'      => 10,
                    'rich'          => [
                        'name' => [
                            'textBorderColor' => 'white',
                            'fontSize'        => 10,
                        ],
                    ],
                ],
            ];
        }

        return $series;
    }
}