<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxLength
{
    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @return int|null
     */
    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @param int $maxLength
     *
     * @return $this
     */
    public function setMaxLength(int $maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }
}