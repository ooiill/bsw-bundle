<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Url
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url ?? '';
    }

    /**
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null)
    {
        $this->url = $url;

        return $this;
    }
}