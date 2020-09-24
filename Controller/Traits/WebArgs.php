<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;

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
}