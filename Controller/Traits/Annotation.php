<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Annotation\Entity\AccessControl;
use Leon\BswBundle\Annotation\Entity\Filter;
use Leon\BswBundle\Annotation\Entity\Output;
use Leon\BswBundle\Annotation\Entity\Persistence;
use Leon\BswBundle\Annotation\Entity\Preview;
use Leon\BswBundle\Annotation\Entity\Mixed as MixedAnnotation;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Entity\Enum;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Leon\BswBundle\Annotation\Entity\Input;
use Leon\BswBundle\Annotation\AnnotationConverter;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @property AbstractController $container
 */
trait Annotation
{
    /**
     * Annotation converter
     *
     * @param string $class
     * @param bool   $new
     *
     * @return AnnotationConverter
     * @throws
     */
    public function annotation(string $class, bool $new = false): AnnotationConverter
    {
        static $pool = [];

        if (!class_exists($class)) {
            throw new AnnotationException("The annotation class {$class} is not found");
        }

        $converter = "{$class}Converter";
        if (!class_exists($converter)) {
            throw new AnnotationException("The annotation converter class {$converter} not exists ({$converter})");
        }

        if ($new) {
            return new $converter();
        }

        if (!isset($pool[$class])) {
            $pool[$class] = new $converter();
        }

        return $pool[$class];
    }

    /**
     * Input annotation parse
     *
     * @param string $class
     * @param string $method
     *
     * @return array
     */
    public function getInputAnnotation(string $class, string $method): array
    {
        $route = $this->getRouteCollection(true);
        $http = current($route[$class][$method]['http']);

        return $this->caching(
            function () use ($class, $method, $http) {

                $annotation = $this->annotation(Input::class)->resolveMethod(
                    $class,
                    $method,
                    ['method' => $http]
                );

                return $annotation[Input::class] ?? [];
            }
        );
    }

    /**
     * Output annotation parse
     *
     * @param string $class
     * @param string $method
     *
     * @return array
     */
    public function getOutputAnnotation(string $class, string $method): array
    {
        return $this->caching(
            function () use ($class, $method) {

                $list = $this->annotation(Output::class)->resolveMethod($class, $method);
                $list = $list[Output::class] ?? [];
                $listHandling = [];

                /**
                 * @param string $position
                 * @param array  $extra
                 */
                $merge = function (string $position, array $extra) use (&$listHandling) {
                    if ($position == Abs::POS_BOTTOM) {
                        $listHandling = array_merge($listHandling, $extra);
                    } elseif ($position == Abs::POS_TOP) {
                        $listHandling = array_merge($extra, $listHandling);
                    } else {
                        $listHandling = Helper::arrayInsertAssoc($listHandling, $position, $extra);
                    }
                };

                /**
                 * @var Output $item
                 */
                foreach ($list as $item) {

                    $extra = [];
                    if (empty($item->extra)) {
                        $prefix = $item->prefix ? "{$item->prefix}." : null;
                        $extra["{$prefix}{$item->field}"] = [
                            'type'   => $item->type,
                            'label'  => $item->label,
                            'trans'  => $item->trans,
                            'tab'    => $item->tab,
                            'enum'   => $item->enum,
                            'prefix' => $item->prefix,
                        ];
                        $merge($item->position, $extra);
                        continue;
                    }

                    if (!$item->extra) {
                        continue;
                    }

                    if (!method_exists($class, $item->extra)) {
                        continue;
                    }

                    $output = call_user_func([$class, $item->extra]) ?? [];
                    $item->tab = $item->tab ?? 0;

                    foreach ($output as $field => $meta) {

                        $trans = $meta['trans'] ?? true;
                        $label = $meta['label'] ?? $field;
                        $tab = $meta['tab'] ?? 0;

                        $meta['label'] = $trans ? Helper::stringToLabel($label) : $label;
                        $meta['trans'] = $trans;
                        $meta['tab'] = $tab + $item->tab + (($tab && $item->tab) ? 1 : 0);
                        $meta['enum'] = $meta['enum'] ?? ($item->enum[$field] ?? []);

                        $prefix = $item->prefix ? "{$item->prefix}." : null;
                        $extra["{$prefix}{$field}"] = array_merge(['type' => Abs::T_STRING], $meta);
                    }

                    $merge($item->position, $extra);
                }

                return $listHandling;
            }
        );
    }

    /**
     * Preview annotation parse
     *
     * @param string $class
     * @param array  $extraArgs
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getPreviewAnnotation(string $class, array $extraArgs = [], string $property = null): array
    {
        $enumClass = $extraArgs['enumClass'] ?? null;
        if (!$enumClass || !Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class is required and should be extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $extraArgs, $property) {

                $converter = $this->annotation(Preview::class);
                $converter->setAnnotationClass(
                    [
                        Type::class,
                        Length::class,
                    ]
                );

                $annotation = $converter->resolveProperty($class, $property, $extraArgs);
                $properties = [];
                foreach ($annotation as $attribute => $item) {
                    if (empty($item[Preview::class])) {
                        continue;
                    }
                    $properties[$attribute] = (array)end($item[Preview::class]);
                }

                return $properties;
            }
        );
    }

    /**
     * Persistence annotation parse
     *
     * @param string $class
     * @param array  $extraArgs
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getPersistenceAnnotation(string $class, array $extraArgs = [], string $property = null): array
    {
        $enumClass = $extraArgs['enumClass'] ?? null;
        if (!$enumClass || !Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class is required and should be extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $extraArgs, $property) {

                $converter = $this->annotation(Persistence::class);
                $converter->setAnnotationClass(
                    [
                        Type::class,
                    ]
                );

                $annotation = $converter->resolveProperty($class, $property, $extraArgs);
                $properties = [];
                foreach ($annotation as $attribute => $item) {
                    if (empty($item[Persistence::class])) {
                        continue;
                    }
                    $properties[$attribute] = (array)end($item[Persistence::class]);
                }

                return $properties;
            }
        );
    }

    /**
     * Filter annotation parse
     *
     * @param string $class
     * @param array  $extraArgs
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getFilterAnnotation(string $class, array $extraArgs = [], string $property = null): array
    {
        $enumClass = $extraArgs['enumClass'] ?? null;
        if (!$enumClass || !Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class is required and should be extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $extraArgs, $property) {

                $converter = $this->annotation(Filter::class);
                $converter->setAnnotationClass(
                    [
                        Type::class,
                    ]
                );

                $split = Abs::FILTER_INDEX_SPLIT;
                $annotation = $converter->resolveProperty($class, $property, $extraArgs);
                $properties = [];
                foreach ($annotation as $attribute => $item) {
                    if (empty($item[Filter::class])) {
                        continue;
                    }

                    foreach ($item[Filter::class] as $index => $filter) {
                        $filter->index = $filter->index ?? $index;
                        $filter->name = $filter->name ?: $attribute;
                        $properties["{$filter->name}{$split}{$filter->index}"] = (array)$filter;
                    }
                }

                return $properties;
            }
        );
    }

    /**
     * Access control annotation parse
     *
     * @param string $class
     *
     * @return array
     * @throws
     */
    public function getAccessControlAnnotation(string $class): array
    {
        return $this->caching(
            function () use ($class) {

                $converter = $this->annotation(AccessControl::class);
                $annotation = $converter->resolveMethod($class);

                $access = [];
                foreach ($annotation['annotation'] as $method => $item) {
                    $access[$method] = current(current($item));
                }

                $classify = $annotation['document']['info'] ?: Helper::clsName($class);

                return [$classify, $access];
            }
        );
    }

    /**
     * Mixed annotation parse
     *
     * @param string $class
     * @param array  $extraArgs
     * @param string $property
     *
     * @return array
     * @throws
     */
    public function getMixedAnnotation(string $class, array $extraArgs = [], string $property = null): array
    {
        $enumClass = $extraArgs['enumClass'] ?? null;
        if (!$enumClass || !Helper::extendClass($enumClass, Enum::class, true)) {
            throw new AnnotationException("Enum class is required and should be extend " . Enum::class);
        }

        return $this->caching(
            function () use ($class, $extraArgs, $property) {

                $converter = $this->annotation(MixedAnnotation::class);
                $annotation = $converter->resolveProperty($class, $property, $extraArgs);
                $properties = [];

                foreach ($annotation as $attribute => $item) {
                    if (empty($item[MixedAnnotation::class])) {
                        continue;
                    }
                    $properties[$attribute] = (array)end($item[MixedAnnotation::class]);
                }

                return $properties;
            }
        );
    }
}