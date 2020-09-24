<?php

namespace Leon\BswBundle\Entity;

use Exception;
use InvalidArgumentException;

class FoundationEntity
{
    /**
     * Getter
     *
     * @param string $name
     *
     * @return mixed
     * @throws
     */
    public function __get(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException("Property " . static::class . "::{$name} is not defined");
        }

        return $this->{$name};
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws
     */
    public function __set(string $name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new InvalidArgumentException("Property " . static::class . "::{$name} is not defined");
        }

        $this->{$name} = $value;
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

    /**
     * Set attributes
     *
     * @param array $attributes
     * @param bool  $filterBlank
     *
     * @throws
     */
    public function attributes(array $attributes, bool $filterBlank = false)
    {
        foreach ($attributes as $name => $value) {
            if (!property_exists($this, $name)) {
                throw new Exception(static::class . " has no property named `{$name}`");
            }

            if ($filterBlank && $value === '') {
                continue;
            }

            $this->{$name} = $value;
        }
    }
}