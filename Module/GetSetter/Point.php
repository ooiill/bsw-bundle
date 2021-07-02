<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Point
{
    /**
     * @var bool
     */
    protected $pointSenseReverse = false;

    /**
     * @var string
     */
    protected $pointGood = 'rgba(95, 184, 120, .6)';

    /**
     * @var string
     */
    protected $pointBad = 'rgba(255, 184, 0, .6)';

    /**
     * @var array
     */
    protected $point = [
        'max' => [
            'type'      => 'max',
            'name'      => 'Max',
            'itemStyle' => ['color' => 'rgba(100, 149, 237, .6)'],
        ],
        'min' => [
            'type'      => 'min',
            'name'      => 'Min',
            'itemStyle' => ['color' => 'rgba(100, 149, 237, .6)'],
        ],
    ];

    /**
     * @return bool
     */
    public function isPointSenseReverse(): bool
    {
        return $this->pointSenseReverse;
    }

    /**
     * @param bool $pointSenseReverse
     *
     * @return $this
     */
    public function setPointSenseReverse(bool $pointSenseReverse = true)
    {
        $this->pointSenseReverse = $pointSenseReverse;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointGood(): string
    {
        return $this->pointGood;
    }

    /**
     * @param string $pointGood
     *
     * @return $this
     */
    public function setPointGood(string $pointGood)
    {
        $this->pointGood = $pointGood;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointBad(): string
    {
        return $this->pointBad;
    }

    /**
     * @param string $pointBad
     *
     * @return $this
     */
    public function setPointBad(string $pointBad)
    {
        $this->pointBad = $pointBad;

        return $this;
    }

    /**
     * @return array
     */
    public function getPoint(): array
    {
        return $this->point;
    }

    /**
     * @param array $point
     *
     * @return $this
     */
    public function setPoint(array $point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setPointField(string $field, $value)
    {
        Helper::setArrayValue($this->point, $field, $value);

        return $this;
    }
}