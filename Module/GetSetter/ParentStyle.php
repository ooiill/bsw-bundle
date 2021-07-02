<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait ParentStyle
{
    /**
     * @var array
     */
    protected $parentStyle = [];

    /**
     * @return string
     */
    public function getParentStyle(): string
    {
        return Helper::jsonStringify($this->parentStyle);
    }

    /**
     * @return array
     */
    public function getParentStyleArray(): array
    {
        return $this->parentStyle;
    }

    /**
     * @return string|null
     */
    public function getParentStyleStringify(): ?string
    {
        return Html::cssStyleFromArray($this->parentStyle);
    }

    /**
     * @param array $parentStyle
     *
     * @return $this
     */
    public function setParentStyle(array $parentStyle)
    {
        $this->parentStyle = $parentStyle;

        return $this;
    }

    /**
     * @param array $parentStyle
     *
     * @return $this
     */
    public function appendParentStyle(array $parentStyle)
    {
        $this->parentStyle = array_merge($this->parentStyle, $parentStyle);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParentStyle(string $name): bool
    {
        return isset($this->parentStyle[$name]);
    }

    /**
     * @param bool|string $value
     *
     * @return $this
     */
    public function setParentDisplay($value)
    {
        if (is_string($value)) {
            $this->appendParentStyle(['display' => $value]);
        } elseif ($value) {
            $this->appendParentStyle(['display' => 'inline-block']);
        } else {
            $this->appendParentStyle(['display' => 'none']);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isParentDisplay(): bool
    {
        return ($this->parentStyle['display'] ?? null) !== 'none';
    }
}