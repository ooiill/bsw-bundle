<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SaveName
{
    /**
     * @var string
     */
    protected $saveName;

    /**
     * @return string
     */
    public function getSaveName(): ?string
    {
        return $this->saveName;
    }

    /**
     * @param string $saveName
     *
     * @return $this
     */
    public function setSaveName(string $saveName)
    {
        $this->saveName = $saveName;

        return $this;
    }
}