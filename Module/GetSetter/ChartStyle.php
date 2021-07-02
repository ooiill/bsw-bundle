<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait ChartStyle
{
    /**
     * @var array
     */
    protected $style = [
        'margin' => '20px auto 0',
        'float'  => 'none',
    ];

    /**
     * @return array
     */
    public function getStyle(): array
    {
        return $this->style;
    }

    /**
     * @return string
     */
    public function getStyleStringify(): string
    {
        return Html::cssStyleFromArray($this->getStyle());
    }

    /**
     * @param array $style
     *
     * @return $this
     */
    public function setStyle(array $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setStyleField(string $field, $value)
    {
        Helper::setArrayValue($this->style, $field, $value);

        return $this;
    }
}