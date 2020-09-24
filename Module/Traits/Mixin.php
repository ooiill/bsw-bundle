<?php

namespace Leon\BswBundle\Module\Traits;

use Leon\BswBundle\Component\Helper;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use InvalidArgumentException;
use Closure;

trait Mixin
{
    /**
     * @var Closure[]
     */
    protected static $macros = [];

    /**
     * @var array
     */
    protected static $macroContextStack = [];

    /**
     * Register a custom macro
     *
     * @param string          $name
     * @param object|callable $macro
     *
     * @return void
     */
    public static function macro($name, $macro)
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Load mixin class
     *
     * @param object|string $mixin
     *
     * @throws ReflectionException
     */
    public static function mixin($mixin)
    {
        $mixin = is_object($mixin) ? $mixin : new $mixin();
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            if ($method->isConstructor() || $method->isDestructor()) {
                continue;
            }
            $method->setAccessible(true);
            self::macro($method->name, $method->invoke($mixin));
        }
    }

    /**
     * Get macro
     *
     * @param string $name
     *
     * @return Closure|null
     */
    public static function getMacro($name)
    {
        return static::$macros[$name] ?? null;
    }

    /**
     * Caller
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return mixed
     * @throws
     */
    public function __call($name, $parameters)
    {
        if ($macro = static::getMacro($name)) {
            $boundMacro = @$macro->bindTo($this, static::class) ?: @$macro->bindTo(null, static::class);

            return call_user_func_array($boundMacro ?: $macro, $parameters);
        }

        if (Helper::strEndWith($name, 'Mixin')) {
            return null;
        }

        throw new InvalidArgumentException("Method " . static::class . "::{$name}() is not defined");
    }
}
