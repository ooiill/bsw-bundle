<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Script
{
    /**
     * @var string
     */
    protected $script;

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script ?? '';
    }

    /**
     * @param string $script
     *
     * @return $this
     */
    public function setScript(string $script)
    {
        $this->script = $script;

        return $this;
    }
}