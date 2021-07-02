<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Api
{
    /**
     * @var string
     */
    protected $api;

    /**
     * @return string
     */
    public function getApi(): string
    {
        return $this->api;
    }

    /**
     * @param string $api
     *
     * @return $this
     */
    public function setApi(string $api)
    {
        $this->api = $api;

        return $this;
    }
}