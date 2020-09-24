<?php

namespace Leon\BswBundle\Module\Hook;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\HookException;
use Leon\BswBundle\Module\Hook\Entity\Enums;

class Dispatcher
{
    /**
     * @var array
     */
    protected $hooks = [];

    /**
     * @var callable
     */
    protected $beforeHandler;

    /**
     * @var callable
     */
    protected $afterHandler;

    /**
     * Set hooks
     *
     * @param array $hooks
     *
     * @return Dispatcher
     * @throws
     */
    public function setHooks(array $hooks): Dispatcher
    {
        foreach ($hooks as $hook => $fields) {
            if (!is_array($fields)) {
                throw new HookException("Fields of {$hook} must be array");
            }
            $this->addHook($hook, $fields);
        }

        return $this;
    }

    /**
     * Set before handler
     *
     * @param callable $handler
     *
     * @return Dispatcher
     */
    public function setBeforeHandler(callable $handler = null): Dispatcher
    {
        $this->beforeHandler = $handler;

        return $this;
    }

    /**
     * Set after handler
     *
     * @param callable $handler
     *
     * @return Dispatcher
     */
    public function setAfterHandler(callable $handler = null): Dispatcher
    {
        $this->afterHandler = $handler;

        return $this;
    }

    /**
     * Add hook
     *
     * @param string $hook
     * @param array  $fields
     *
     * @return array
     * @throws
     */
    public function addHook(string $hook, array $fields)
    {
        if (!Helper::extendClass($hook, Hook::class)) {
            throw new HookException("Hook {$hook} must be class extend " . Hook::class);
        }

        if (!isset($this->hooks[$hook])) {
            return $this->hooks[$hook] = $fields;
        }

        return $this->hooks[$hook] = array_merge($this->hooks[$hook], $fields);
    }

    /**
     * Hook
     *
     * @param Hook  $hook
     * @param mixed $item
     * @param array $fields
     * @param bool  $persistence
     * @param array $extraArgs
     *
     * @return mixed
     * @throws
     */
    private function hook(Hook $hook, $item, array $fields, bool $persistence, array $extraArgs)
    {
        $hooker = get_class($hook);

        foreach ($fields as $field => $args) {

            if (is_numeric($field)) {
                [$field, $args] = [$args, []];
            }

            if (!is_array($args)) {
                throw new HookException("Arguments of {$hooker}.{$field} must be array");
            }

            $extra = $extraArgs[$hooker] ?? [];
            if (is_array($extra[Abs::HOOKER_FLAG_FIELDS][$field] ?? null)) {
                $extra = $extra[Abs::HOOKER_FLAG_FIELDS][$field];
            } else {
                unset($extra[Abs::HOOKER_FLAG_FIELDS]);
            }

            if (isset($extraArgs[Abs::HOOKER_FLAG_ACME]) && is_array($extraArgs[Abs::HOOKER_FLAG_ACME])) {
                $extra = array_merge($extraArgs[Abs::HOOKER_FLAG_ACME], $extra);
            }

            $item = $hook->hook($item, $field, $args, $persistence, $extra);
        }

        return $item;
    }

    /**
     * Execute
     *
     * @param mixed $item
     * @param int   $index
     * @param bool  $persistence
     * @param array $extraArgs
     *
     * @return mixed
     * @throws
     */
    public function execute($item, int $index, bool $persistence = true, array $extraArgs = [])
    {
        static $instance = [];

        // before handler
        $original = $item;
        if ($this->beforeHandler) {

            $type = gettype($item);
            $item = call_user_func_array($this->beforeHandler, [$item, $extraArgs, $index]);

            if (is_array($this->beforeHandler) && isset($this->beforeHandler[1])) {
                $info = "{$this->beforeHandler[1]}():{$type}";
            }

            Helper::callReturnType($item, [$type, Abs::T_NULL], $info ?? null);
            if (!$item) {
                return $item;
            }
        }

        // hooks
        foreach ($this->hooks as $hook => $fields) {
            if (!isset($instance[$hook])) {
                $instance[$hook] = new $hook();
            }
            $item = $this->hook($instance[$hook], $item, $fields, $persistence, $extraArgs);
        }

        // after handler
        if ($this->afterHandler) {

            $type = gettype($item);
            $item = call_user_func_array($this->afterHandler, [$item, $original, $extraArgs, $index]);

            if (is_array($this->afterHandler) && isset($this->afterHandler[1])) {
                $info = "{$this->afterHandler[1]}():{$type}";
            }

            Helper::callReturnType($item, [$type, Abs::T_NULL], $info ?? null);
            if (!$item) {
                return $item;
            }
        }

        return $item;
    }

    /**
     * Execute multiple
     *
     * @param array $items
     * @param bool  $persistence
     * @param array $extraArgs
     *
     * @return array
     * @throws
     */
    public function executeAny(array $items, bool $persistence = true, array $extraArgs = [])
    {
        $itemsHandling = [];
        foreach ($items as $key => $item) {
            $item = $this->execute($item, $key, $persistence, $extraArgs);
            if ($item) {
                $itemsHandling[$key] = $item;
            }
        }

        return $itemsHandling;
    }
}