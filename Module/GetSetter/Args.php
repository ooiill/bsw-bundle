<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait Args
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var bool
     */
    protected $argsUseMeta = false;

    /**
     * @param bool|null $meta
     *
     * @return array
     */
    public function getArgs(?bool $meta = null): array
    {
        if (isset($meta)) {
            return $meta ? $this->args : Helper::urlEncodeValues($this->args);
        }

        return $this->isArgsUseMeta() ? $this->args : Helper::urlEncodeValues($this->args);
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getArgsItem(string $key)
    {
        return $this->getArgs()[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getArgsString(): string
    {
        return Html::paramsBuilder($this->getArgs());
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function appendArgs(array $args)
    {
        $this->args = array_merge($this->args, $args);

        return $this;
    }

    /**
     * @return bool
     */
    public function isArgsUseMeta(): bool
    {
        return $this->argsUseMeta;
    }

    /**
     * @param bool $argsUseMeta
     *
     * @return $this
     */
    public function setArgsUseMeta(bool $argsUseMeta = true)
    {
        $this->argsUseMeta = $argsUseMeta;

        return $this;
    }
}