<?php

namespace Leon\BswBundle\Annotation;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Leon\BswBundle\Module\Interfaces\AnnotationConverterInterface;
use Leon\BswBundle\Component\Reflection;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AnnotationConverter implements AnnotationConverterInterface
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * @var Reflection
     */
    protected $reflection;

    /**
     * @var array
     */
    protected $annotationClass;

    /**
     * @var array
     */
    protected $extraArgs;

    /**
     * @var string
     */
    public $scene;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $target;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var Annotation
     */
    protected $item;

    /**
     * @var array
     */
    protected $items;

    /**
     * AnnotationConverter constructor.
     *
     * @throws
     */
    public function __construct()
    {
        $this->reader = new AnnotationReader();
        $this->reflection = new Reflection();
        $this->annotationClass = [str_replace('Converter', null, static::class)];

        $this->scene = str_replace('Converter', null, Helper::clsName(static::class));
        $this->scene = Helper::camelToUnder($this->scene);
    }

    /**
     * Show exception
     *
     * @param string $field
     * @param string $info
     *
     * @throws
     */
    public function exception(string $field, string $info)
    {
        $class = current($this->annotationClass);
        $annotation = "@{$class}() in {$this->class}::{$this->target}";

        throw new AnnotationException(
            "Annotation {$annotation} option `{$field}` {$info}"
        );
    }

    /**
     * Set annotation class
     *
     * @param array $annotation
     * @param bool  $keep
     *
     * @return array
     * @license The master annotation should be first
     */
    public function setAnnotationClass(array $annotation, bool $keep = true): array
    {
        if ($keep) {
            return $this->annotationClass = array_merge($this->annotationClass, $annotation);
        }

        return $this->annotationClass = $annotation;
    }

    /**
     * Converter
     *
     * @param array $annotation
     *
     * @return array
     */
    public function converter(array $annotation): array
    {
        $list = [];
        $this->items = [];

        foreach ($annotation as $key => $item) {

            $class = get_class($item);
            $this->items[$class] = $item;

            if (!in_array($class, $this->annotationClass)) {
                continue;
            }

            // just handler the first annotation class
            if ($class != $this->annotationClass[0]) {
                $list[$class][] = $item;
                continue;
            }

            $this->item = $item;
            foreach ($item as $attr => $value) {
                if (!method_exists($this, $attr)) {
                    continue;
                }

                $item->{$attr} = call_user_func_array([$this, $attr], [$value]);
                if (isset($this->extraArgs[$attr]) && is_null($item->{$attr})) {
                    $item->{$attr} = $this->extraArgs[$attr];
                }

                $this->item = $item;
            }

            $list[$class][] = $item;
        }

        return $list;
    }

    /**
     * Annotation alone method
     *
     * @param ReflectionMethod $method
     *
     * @return array
     */
    public function annotationAloneMethod(ReflectionMethod $method): array
    {
        $this->class = $method->class;
        $this->target = $method->name;
        $annotation = $this->reader->getMethodAnnotations($method);

        return $this->converter($annotation);
    }

    /**
     * Annotation all method
     *
     * @param ReflectionClass $class
     *
     * @return array
     */
    public function annotationAllMethod(ReflectionClass $class): array
    {
        $all = [];
        foreach ($class->getMethods() as $method) {
            if ($class->name !== $method->class) {
                continue;
            }
            if ($alone = $this->annotationAloneMethod($method)) {
                $all[$method->name] = $alone;
            }
        }

        return $all;
    }

    /**
     * Annotation alone property
     *
     * @param ReflectionProperty $property
     *
     * @return array
     */
    public function annotationAloneProperty(ReflectionProperty $property): array
    {
        $this->class = $property->class;
        $this->target = $property->name;

        $property->setAccessible(true);
        $this->value = $property->getValue(new $property->class);

        $annotation = $this->reader->getPropertyAnnotations($property);

        return $this->converter($annotation);
    }

    /**
     * Annotation all property
     *
     * @param ReflectionClass $class
     *
     * @return array
     */
    public function annotationAllProperty(ReflectionClass $class): array
    {
        $all = [];
        foreach ($class->getProperties() as $property) {
            if ($class->name !== $property->class) {
                continue;
            }
            if ($alone = $this->annotationAloneProperty($property)) {
                $all[$property->name] = $alone;
            }
        }

        return $all;
    }

    /**
     * Resolve class method
     *
     * @param string $class
     * @param string $method
     * @param array  $extraArgs
     *
     * @return array
     * @throws
     */
    public function resolveMethod(string $class, string $method = null, array $extraArgs = []): array
    {
        $this->extraArgs = $extraArgs;

        if ($method) {
            return $this->annotationAloneMethod(new ReflectionMethod($class, $method));
        }

        return [
            'document'   => $this->reflection->getClsDocByIS($reflectionClass = new ReflectionClass($class)),
            'annotation' => $this->annotationAllMethod($reflectionClass),
        ];
    }

    /**
     * Resolve class property
     *
     * @param string $class
     * @param string $property
     * @param array  $extraArgs
     *
     * @return array
     * @throws
     */
    public function resolveProperty(string $class, string $property = null, array $extraArgs = []): array
    {
        $this->extraArgs = $extraArgs;

        if ($property) {
            return $this->annotationAloneProperty(new ReflectionProperty($class, $property));
        }

        return $this->annotationAllProperty(new ReflectionClass($class));
    }
}