<?php

namespace Leon\BswBundle\Module\Traits;

use InvalidArgumentException;

trait Magic
{
    /**
     * Getter
     *
     * @param string $name
     *
     * @return mixed
     * @throws
     */
    protected function getAttribute(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException("Property " . static::class . "::{$name} is not defined");
        }

        return $this->{$name};
    }

    /**
     * Dynamic get attribute
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function dispatchAttribute(string $name, $default = null)
    {
        if (!property_exists($this, $name)) {
            return $default;
        }

        return static::${$name} ?? ($this->{$name} ?? $default);
    }

    /**
     * Dynamic call method
     *
     * @param string $name
     * @param mixed  $default
     * @param array  $params
     *
     * @return mixed
     */
    public function dispatchMethod(string $name, $default = null, array $params = [])
    {
        if (!method_exists($this, $name)) {
            return $default;
        }

        return call_user_func_array([$this, $name], $params) ?? $default;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws
     */
    protected function setAttribute(string $name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException("Property " . static::class . "::{$name} is not defined");
        }

        $this->{$name} = $value;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     *
     * @throws
     */
    protected function attributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            if (!property_exists($this, $name)) {
                throw new InvalidArgumentException("Property " . static::class . "::{$name} is not defined");
            }
            $this->{$name} = $value;
        }
    }

    /**
     * Isset
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return property_exists($this, $name);
    }
}