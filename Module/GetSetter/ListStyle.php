<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait ListStyle
{
    /**
     * @var array
     */
    protected $listStyle = [
        'width'  => '240px',
        'height' => '300px',
    ];

    /**
     * @return string
     */
    public function getListStyle(): string
    {
        return Helper::jsonFlexible($this->listStyle);
    }

    /**
     * @return string|null
     */
    public function getListStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->listStyle);
    }

    /**
     * @param array $listStyle
     *
     * @return $this
     */
    public function setListStyle(array $listStyle)
    {
        $this->listStyle = $listStyle;

        return $this;
    }

    /**
     * @param array $listStyle
     *
     * @return $this
     */
    public function appendListStyle(array $listStyle)
    {
        $this->listStyle = array_merge($this->listStyle, $listStyle);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasListStyle(string $name): bool
    {
        return isset($this->listStyle[$name]);
    }
}