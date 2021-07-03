<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Component\Helper;
use stdClass;

class Arguments extends stdClass
{
    /**
     * Set argument
     *
     * @param string $key
     * @param mixed  $value
     * @param bool   $unsetWhenNull
     *
     * @return $this
     */
    public function set(string $key, $value, bool $unsetWhenNull = true)
    {
        if (is_null($value) && $unsetWhenNull) {
            return $this->unset($key);
        }

        $this->{$key} = $value;

        return $this;
    }

    /**
     * Unset
     *
     * @param string $key
     *
     * @return $this
     */
    public function unset(string $key)
    {
        unset($this->{$key});

        return $this;
    }

    /**
     * Set many arguments
     *
     * @param array $target
     * @param bool  $unsetWhenNull
     *
     * @return $this
     */
    public function setMany(array $target, bool $unsetWhenNull = false)
    {
        foreach ($target as $key => $value) {
            $this->set($key, $value, $unsetWhenNull);
        }

        return $this;
    }

    /**
     * Rename attribute
     *
     * @param string $from
     * @param string $to
     *
     * @return $this
     */
    public function rename(string $from, string $to)
    {
        $this->set($to, $this->get($from));
        $this->set($from, null);

        return $this;
    }

    /**
     * Get argument
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->isset($key) ? $this->{$key} : null;
    }

    /**
     * Get any arguments
     *
     * @param array $keys
     * @param bool  $withKey
     *
     * @return array
     */
    public function getAny(array $keys, bool $withKey = false)
    {
        $target = [];
        foreach ($keys as $key) {
            if ($withKey) {
                $target[$key] = $this->get($key);
            } else {
                array_push($target, $this->get($key));
            }
        }

        return $target;
    }

    /**
     * Get all arguments
     *
     * @return array
     */
    public function getAll(): array
    {
        return Helper::entityToArray($this);
    }

    /**
     * Isset argument
     *
     * @param string $key
     *
     * @return bool
     */
    public function isset(string $key): bool
    {
        return isset($this->{$key});
    }

    /**
     * __getter
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}