<?php

namespace Leon\BswBundle\Annotation;

use Leon\BswBundle\Module\Exception\AnnotationException;

/**
 * @Annotation
 */
class Annotation
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * Annotation constructor.
     *
     * @param array $options
     *
     * @throws
     */
    public function __construct(array $options)
    {
        foreach ($options as $attribute => $value) {
            if (!property_exists($this, $attribute)) {
                throw new AnnotationException(static::class . " has no field named `{$attribute}`");
            }
            $this->{$attribute} = $value;
        }
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
            throw new AnnotationException(static::class . " property `{$name}` does not exist");
        }

        $this->{$name} = $value;
    }
}