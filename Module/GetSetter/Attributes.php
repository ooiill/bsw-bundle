<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Html;

trait Attributes
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return string
     */
    public function getAttributes(): string
    {
        return Html::renderTagAttributes($this->attributes);
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function appendAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttributes(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}