<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait TokenSeparators
{
    /**
     * @var array
     */
    protected $tokenSeparators = [';', '；'];

    /**
     * @return string
     */
    public function getTokenSeparators(): string
    {
        return Helper::jsonStringify($this->tokenSeparators);
    }

    /**
     * @param array $tokenSeparators
     *
     * @return $this
     */
    public function setTokenSeparators(array $tokenSeparators)
    {
        $this->tokenSeparators = $tokenSeparators;

        return $this;
    }

    /**
     * @param array $tokenSeparators
     *
     * @return $this
     */
    public function appendTokenSeparators(array $tokenSeparators)
    {
        $this->tokenSeparators = array_merge($this->tokenSeparators, $tokenSeparators);

        return $this;
    }

    /**
     * @param string $tokenSeparator
     *
     * @return $this
     */
    public function pushTokenSeparators(string $tokenSeparator)
    {
        array_push($this->tokenSeparators, $tokenSeparator);

        return $this;
    }
}