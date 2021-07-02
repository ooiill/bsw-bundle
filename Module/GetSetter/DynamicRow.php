<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait DynamicRow
{
    /**
     * @var bool
     */
    protected $dynamicRow = false;

    /**
     * @var string
     */
    protected $dynamicRowAdd;

    /**
     * @var string
     */
    protected $dynamicRowSub;

    /**
     * @var string
     */
    protected $dynamicRowLabel = 'Add field';

    /**
     * @var array
     */
    protected $dynamicRowButtonStyle = ['width' => '100%'];

    /**
     * @return bool
     */
    public function isDynamicRow(): bool
    {
        return $this->dynamicRow;
    }

    /**
     * @param bool $dynamicRow
     *
     * @return $this
     */
    public function setDynamicRow(bool $dynamicRow)
    {
        $this->dynamicRow = $dynamicRow;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowAdd(): ?string
    {
        return $this->dynamicRowAdd;
    }

    /**
     * @param string $dynamicRowAdd
     *
     * @return $this
     */
    public function setDynamicRowAdd(string $dynamicRowAdd)
    {
        $this->dynamicRowAdd = $dynamicRowAdd;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowSub(): ?string
    {
        return $this->dynamicRowSub;
    }

    /**
     * @param string $dynamicRowSub
     *
     * @return $this
     */
    public function setDynamicRowSub(string $dynamicRowSub)
    {
        $this->dynamicRowSub = $dynamicRowSub;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowLabel(): string
    {
        return $this->dynamicRowLabel;
    }

    /**
     * @param string $dynamicRowLabel
     *
     * @return $this
     */
    public function setDynamicRowLabel(string $dynamicRowLabel)
    {
        $this->dynamicRowLabel = $dynamicRowLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowButtonStyle(): string
    {
        return Helper::jsonStringify($this->dynamicRowButtonStyle);
    }

    /**
     * @return array
     */
    public function getDynamicRowButtonStyleArray(): array
    {
        return $this->dynamicRowButtonStyle;
    }

    /**
     * @return string|null
     */
    public function getDynamicRowButtonStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->dynamicRowButtonStyle);
    }

    /**
     * @param array $dynamicRowButtonStyle
     *
     * @return $this
     */
    public function setDynamicRowButtonStyle(array $dynamicRowButtonStyle)
    {
        $this->dynamicRowButtonStyle = $dynamicRowButtonStyle;

        return $this;
    }

    /**
     * @param array $dynamicRowButtonStyle
     *
     * @return $this
     */
    public function appendDynamicRowButtonStyle(array $dynamicRowButtonStyle)
    {
        $this->dynamicRowButtonStyle = array_merge($this->dynamicRowButtonStyle, $dynamicRowButtonStyle);

        return $this;
    }
}