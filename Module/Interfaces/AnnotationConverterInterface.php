<?php

namespace Leon\BswBundle\Module\Interfaces;

interface AnnotationConverterInterface
{
    /**
     * Resolve class method
     *
     * @param string $class
     * @param string $method
     * @param array  $default
     *
     * @return array
     * @throws
     */
    public function resolveMethod(string $class, string $method = null, array $default = []): array;

    /**
     * Resolve class property
     *
     * @param string $class
     * @param string $property
     * @param array  $default
     *
     * @return array
     * @throws
     */
    public function resolveProperty(string $class, string $property = null, array $default = []): array;
}