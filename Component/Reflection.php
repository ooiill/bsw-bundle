<?php

namespace Leon\BswBundle\Component;

use Reflection as Ref;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionClassConstant;
use InvalidArgumentException;

class Reflection
{
    /**
     * @var array
     */
    protected $tags = [
        'common'    => [
            'access',
            'link',
            'since',
            'version',
        ],
        'class'     => [
            'author',
            'copyright',
        ],
        'method'    => [
            'author',
            'copyright',
            'deprecated',
            'throws',
            'param',
            'property',
            'return',
            'var',
            'tutorial',
            'license-request',
            'license-response',
            'license',
        ],
        'attribute' => [
            'var',
        ],
        'const'     => [
            'const',
        ],
    ];

    /**
     * @var string
     */
    protected $commentFilter = "\r\n\t *#";

    /**
     * @var string
     */
    protected $tagRegular = '/@(%s)([^\w].*)/i';

    /**
     * Set tags
     *
     * @param string $key
     * @param array  $value
     *
     * @return void
     */
    public function setTags(string $key, array $value)
    {
        if (isset($this->tags[$key])) {
            $this->tags[$key] = $value;
        }
    }

    /**
     * Pre processing for document
     *
     * @param string $doc
     * @param string $type
     *
     * @return array
     */
    public function preProcessingDoc(string $doc, $type = null): array
    {
        $comment = array_slice(explode(PHP_EOL, $doc), 1, -1);
        foreach ($comment as &$item) {
            $item = trim($item, $this->commentFilter);
        }

        $comment = array_filter($comment);

        if (!$type || !isset($this->tags[$type])) {
            return $comment;
        }

        $tags = array_merge($this->tags['common'], $this->tags[$type]);
        $tagsReg = implode('|', $tags);

        return [
            $comment,
            $tagsReg,
        ];
    }

    // ---

    /**
     * Resolve class document string
     *
     * @param string          $doc
     * @param ReflectionClass $instance
     *
     * @return array
     */
    public function resolveClsDocStr(string $doc, ReflectionClass $instance = null): array
    {
        [$doc, $tagsReg] = $this->preProcessingDoc($doc, 'class');
        $docArray = ['info' => null];

        foreach ($doc as $item) {
            preg_match(sprintf($this->tagRegular, $tagsReg), $item, $result);

            if (empty($result)) {
                if (strpos($item, '@') !== 0 && empty($docArray['info'])) {
                    $docArray['info'] = trim($item);
                }
                continue;
            }

            [$tagName, $tagValue] = array_slice($result, 1);
            $docArray[$tagName] = trim($tagValue);
        }

        $instance && $docArray['proto'] = $instance;
        $docArray['info'] = rtrim($docArray['info'], PHP_EOL);

        return $docArray;
    }

    /**
     * Get class document
     *
     * @param string $cls
     * @param bool   $prototype
     *
     * @return array
     * @throws
     */
    public function getClsDoc(string $cls, bool $prototype = false): array
    {
        if (!class_exists($cls)) {
            throw new InvalidArgumentException("Class {$cls} is not found");
        }

        $instance = new ReflectionClass($cls);

        return $this->resolveClsDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    /**
     * Get class document by instance
     *
     * @param ReflectionClass $instance
     * @param bool            $prototype
     *
     * @return array
     */
    public function getClsDocByIS(ReflectionClass $instance, bool $prototype = false): array
    {
        return $this->resolveClsDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    // ---

    /**
     * Resolve method document string
     *
     * @param string           $doc
     * @param ReflectionMethod $instance
     *
     * @return array
     */
    public function resolveFnDocStr(string $doc, ReflectionMethod $instance = null): array
    {
        [$doc, $tagsReg] = $this->preProcessingDoc($doc, 'method');
        $docArray = ['info' => null];

        foreach ($doc as $item) {
            preg_match(sprintf($this->tagRegular, $tagsReg), $item, $result);
            $result = array_filter($result);

            if (empty($result) || count($result) < 2) {
                if (strpos($item, '@') !== 0 && empty($docArray['info'])) {
                    $docArray['info'] = trim($item);
                }
                continue;
            }

            [$tagName, $tagValue] = array_values(array_slice($result, 1) + [4 => null]);
            $tagValue = trim($tagValue);

            switch ($tagName) {
                case 'deprecated':
                    $docArray[$tagName] = true;
                    break;

                case 'param':
                case 'property':
                case 'var':
                    if (empty($tagValue)) {
                        break;
                    }

                    [$name, $value] = $this->handlerTagParamOrVarString($tagValue);
                    $docArray[$tagName][$name] = $value;
                    break;

                case 'license-request':
                case 'license-response':
                case 'license':
                    $docArray[$tagName][] = $tagValue;
                    break;

                default:
                    $docArray[$tagName] = $tagValue;
                    break;
            }
        }

        if ($instance) {
            $docArray['proto'] = $instance;
            $docArray = $this->resolvePrototype($docArray, $instance);
        }

        $docArray['info'] = rtrim($docArray['info'], PHP_EOL);

        return $docArray;
    }

    /**
     * Get method document
     *
     * @param string $cls
     * @param string $method
     * @param bool   $prototype
     *
     * @return array
     * @throws
     */
    public function getFnDoc(string $cls, string $method, bool $prototype = false): array
    {
        if (!class_exists($cls)) {
            throw new InvalidArgumentException("Class {$cls} is not found");
        }

        if (!method_exists($cls, $method)) {
            throw new InvalidArgumentException("Method {$cls}::{$method}() is not defined");
        }

        $instance = new ReflectionMethod($cls, $method);

        return $this->resolveFnDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    /**
     * Get method document by instance
     *
     * @param ReflectionMethod $instance
     * @param bool             $prototype
     *
     * @return array
     */
    public function getFnDocByIS(ReflectionMethod $instance, bool $prototype = false): array
    {
        return $this->resolveFnDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    // ---

    /**
     * Resolve property document string
     *
     * @param string             $doc
     * @param ReflectionProperty $instance
     *
     * @return array
     */
    public function resolveAttrDocStr(string $doc, ReflectionProperty $instance = null): array
    {
        [$doc, $tagsReg] = $this->preProcessingDoc($doc, 'attribute');
        $docArray = [];

        foreach ($doc as $item) {
            preg_match(sprintf($this->tagRegular, $tagsReg), $item, $result);
            $result = array_filter($result);

            if (empty($result) || count($result) < 2) {
                continue;
            }

            [$tagName, $tagValue] = array_values(array_slice($result, 1) + [4 => null]);
            $tagValue = trim($tagValue);

            switch ($tagName) {
                case 'var':
                    if (empty($tagValue)) {
                        break;
                    }

                    [$name, $value] = $this->handlerTagParamOrVarString($tagValue);
                    $docArray[$tagName][$name] = $value;
                    break;

                default:
                    $docArray[$tagName] = $tagValue;
                    break;
            }

            if ($instance) {
                $docArray['proto'] = $instance;
                $docArray = $this->resolvePrototype($docArray, $instance);
            }
        }

        return $docArray;
    }

    /**
     * Get property document
     *
     * @param string $cls
     * @param string $property
     * @param bool   $prototype
     *
     * @return array
     * @throws
     */
    public function getAttrDoc(string $cls, string $property, bool $prototype = false): array
    {
        if (!class_exists($cls)) {
            throw new InvalidArgumentException("Class {$cls} is not found");
        }

        if (!property_exists($cls, $property)) {
            throw new InvalidArgumentException("Property {$cls}::{$property} is not defined");
        }

        $instance = new ReflectionProperty($cls, $property);

        return $this->resolveAttrDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    /**
     * Get property document by instance
     *
     * @param ReflectionProperty $instance
     * @param bool               $prototype
     *
     * @return array
     */
    public function getAttrDocByIS(ReflectionProperty $instance, bool $prototype = false): array
    {
        return $this->resolveAttrDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    // ---

    /**
     * Resolve const document string
     *
     * @param string                  $doc
     * @param ReflectionClassConstant $instance
     *
     * @return array
     */
    public function resolveConstDocStr(string $doc, ReflectionClassConstant $instance = null): array
    {
        [$doc, $tagsReg] = $this->preProcessingDoc($doc, 'const');
        $docArray = [];

        foreach ($doc as $item) {
            preg_match(sprintf($this->tagRegular, $tagsReg), $item, $result);
            $result = array_filter($result);

            if (empty($result) || count($result) < 2) {
                continue;
            }

            [$tagName, $tagValue] = array_values(array_slice($result, 1) + [4 => null]);
            $tagValue = trim($tagValue);

            switch ($tagName) {
                case 'var':
                    if (empty($tagValue)) {
                        break;
                    }

                    [$name, $value] = $this->handlerTagParamOrVarString($tagValue);
                    $docArray[$tagName][$name] = $value;
                    break;

                default:
                    $docArray[$tagName] = $tagValue;
                    break;
            }

            if ($instance) {
                $docArray['proto'] = $instance;
            }
        }

        return $docArray;
    }

    /**
     * Get const document
     *
     * @param string $cls
     * @param string $const
     * @param bool   $prototype
     *
     * @return array
     * @throws
     */
    public function getConstDoc(string $cls, string $const, bool $prototype = false): array
    {
        if (!class_exists($cls)) {
            throw new InvalidArgumentException("Class {$cls} is not found");
        }

        $instance = new ReflectionClassConstant($cls, $const);

        return $this->resolveConstDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    /**
     * Get cls const document
     *
     * @param string $cls
     * @param bool   $prototype
     *
     * @return array
     * @throws
     */
    public function getClsConstDoc(string $cls, bool $prototype = false): array
    {
        $document = [];
        $const = new ReflectionClass($cls);

        foreach ($const->getReflectionConstants() as $instance) {
            $document[$instance->getName()] = $this->getConstDocByIS($instance, $prototype);
        }

        return $document;
    }

    /**
     * Get const document by instance
     *
     * @param ReflectionClassConstant $instance
     * @param bool                    $prototype
     *
     * @return array
     */
    public function getConstDocByIS(ReflectionClassConstant $instance, bool $prototype = false): array
    {
        return $this->resolveConstDocStr($instance->getDocComment(), $prototype ? $instance : null);
    }

    // ---

    /**
     * Handler param or var's tag string
     *
     * @param string $value
     *
     * @return array
     */
    private function handlerTagParamOrVarString(string $value): array
    {
        $tagValue = explode(' ', $value);
        $tagValue = array_values(
            array_filter($tagValue) + [
                4 => null,
                5 => null,
                6 => null,
            ]
        );

        [$type, $name] = $tagValue;
        $info = trim(implode(' ', array_slice($tagValue, 2)));

        return [
            trim($name, '&$'),
            compact('type', 'info'),
        ];
    }

    /**
     * Get prototype
     *
     * @param array                                 $docArray
     * @param ReflectionMethod | ReflectionProperty $instance
     *
     * @return array
     */
    private function resolvePrototype(array $docArray, $instance)
    {
        if ($instance->isPublic()) {
            $modifier = 'public';
        } elseif ($instance->isProtected()) {
            $modifier = 'protected';
        } else {
            $modifier = 'private';
        }

        $name = $instance->name;
        $modifiers = Ref::getModifierNames($instance->getModifiers());

        if (is_a($instance, ReflectionProperty::class)) {
            if (($default = $instance->isDefault()) && $instance->isStatic()) {
                $modifier != 'public' && $instance->setAccessible(true);
                $default_value = $instance->getValue();
            }
            $own = compact('default', 'default_value');
        } else {
            $param = [];
            foreach ($instance->getParameters() as $p) {
                $docParam = $docArray['param'][$p->name] ?? [];
                $param[$p->name] = array_merge(
                    $docParam,
                    [
                        'proto' => $p,
                        'type'  => (string)$p->getType(),
                    ]
                );
            }
            $own = compact('param');
        }

        return array_merge($docArray, compact('name', 'modifier', 'modifiers'), $own);
    }

    // ---

    /**
     * Property exists self only
     *
     * @param string $cls
     * @param string $property
     *
     * @return bool
     * @throws
     */
    public function propertyExistsSelf(string $cls, string $property)
    {
        $instance = new ReflectionProperty($cls, $property);
        $class = $instance->getDeclaringClass()->getName();

        return $cls === $class;
    }
}