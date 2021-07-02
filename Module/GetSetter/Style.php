<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait Style
{
    /**
     * @var array
     */
    protected $style = [];

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return Helper::jsonStringify($this->style);
    }

    /**
     * @return array
     */
    public function getStyleArray(): array
    {
        return $this->style;
    }

    /**
     * @return string|null
     */
    public function getStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->style);
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
     * @param array $style
     *
     * @return $this
     */
    public function appendStyle(array $style)
    {
        $this->style = array_merge($this->style, $style);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasStyle(string $name): bool
    {
        return isset($this->style[$name]);
    }

    /**
     * @param bool|string $value
     *
     * @return $this
     */
    public function setDisplay($value)
    {
        if (is_string($value)) {
            $this->appendStyle(['display' => $value]);
        } elseif ($value) {
            $this->appendStyle(['display' => 'inline-block']);
        } else {
            $this->appendStyle(['display' => 'none']);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplay(): bool
    {
        return ($this->style['display'] ?? null) !== 'none';
    }
}