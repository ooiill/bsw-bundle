<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait ButtonStyle
{
    /**
     * @var array
     */
    protected $buttonStyle = ['marginTop' => '6px'];

    /**
     * @return string
     */
    public function getButtonStyle(): string
    {
        return Helper::jsonStringify($this->buttonStyle);
    }

    /**
     * @return array
     */
    public function getButtonStyleArray(): array
    {
        return $this->buttonStyle;
    }

    /**
     * @return string|null
     */
    public function getButtonStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->buttonStyle);
    }

    /**
     * @param array $buttonStyle
     *
     * @return $this
     */
    public function setButtonStyle(array $buttonStyle)
    {
        $this->buttonStyle = $buttonStyle;

        return $this;
    }

    /**
     * @param array $buttonStyle
     *
     * @return $this
     */
    public function appendButtonStyle(array $buttonStyle)
    {
        $this->buttonStyle = array_merge($this->buttonStyle, $buttonStyle);

        return $this;
    }
}