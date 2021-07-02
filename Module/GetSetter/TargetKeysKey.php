<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TargetKeysKey
{
    /**
     * @var string
     */
    protected $targetKeysKey;

    /**
     * @return string
     */
    public function getTargetKeysKey(): ?string
    {
        return $this->targetKeysKey;
    }

    /**
     * @param string $targetKeysKey
     *
     * @return $this
     */
    public function setTargetKeysKey(string $targetKeysKey)
    {
        $this->targetKeysKey = $targetKeysKey;

        return $this;
    }
}