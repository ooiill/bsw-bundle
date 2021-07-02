<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait DropdownStyle
{
    /**
     * @var array
     */
    protected $dropdownStyle = [
        'maxHeight' => '500px',
        'overflow'  => 'auto',
    ];

    /**
     * @return string
     */
    public function getDropdownStyle(): string
    {
        return Helper::jsonFlexible($this->dropdownStyle);
    }

    /**
     * @return string|null
     */
    public function getDropdownStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->dropdownStyle);
    }

    /**
     * @param array $dropdownStyle
     *
     * @return $this
     */
    public function setDropdownStyle(array $dropdownStyle)
    {
        $this->dropdownStyle = $dropdownStyle;

        return $this;
    }

    /**
     * @param array $dropdownStyle
     *
     * @return $this
     */
    public function appendDropdownStyle(array $dropdownStyle)
    {
        $this->dropdownStyle = array_merge($this->dropdownStyle, $dropdownStyle);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasDropdownStyle(string $name): bool
    {
        return isset($this->dropdownStyle[$name]);
    }
}