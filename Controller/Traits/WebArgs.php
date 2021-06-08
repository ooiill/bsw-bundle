<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;

trait WebArgs
{
    /**
     * @var array
     */
    protected $logic = [];

    /**
     * Set value by key
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function logicSet(string $key, $value)
    {
        $this->logic[$key] = $value;

        return $this;
    }

    /**
     * Set value by key
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return $this
     */
    public function logicGet(string $key, $default = null)
    {
        return $this->logic[$key] ?? $default;
    }

    /**
     * Merge value by key
     *
     * @param string $key
     * @param array  $value
     *
     * @return $this
     */
    public function logicMerge(string $key, array $value)
    {
        if (!isset($this->logic[$key])) {
            $this->logic[$key] = [];
        }

        $this->logic[$key] = array_merge($this->logic[$key], $value);
        $this->logic[$key] = Helper::moreDimensionArrayUnique($this->logic[$key]);

        return $this;
    }

    /**
     * Decode args for $_GET
     *
     * @param string $appoint
     * @param bool   $isArray
     *
     * @return mixed
     */
    public function getDecodeArgs(string $appoint, bool $isArray = true)
    {
        $value = $this->getArgs($appoint, false);
        if ($value) {
            $value = Helper::safeBase64Decode($value);
        }
        if ($isArray) {
            $value = Helper::parseJsonString($value);
        }

        return $value;
    }

    /**
     * Decode args for $_GET
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function encodeValueForGet($value)
    {
        if (is_array($value)) {
            $value = Helper::jsonStringify($value);
        }
        $value = Helper::safeBase64Encode($value);

        return $value;
    }
}