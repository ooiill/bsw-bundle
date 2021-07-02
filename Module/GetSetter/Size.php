<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait Size
{
    /**
     * @var string
     */
    protected $size = Abs::SIZE_LARGE;

    /**
     * @var bool
     */
    protected $sizeManual = false;

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     *
     * @return $this
     */
    public function setSize(string $size)
    {
        $this->size = $size;
        $this->setSizeManual(true);

        return $this;
    }

    /**
     * @return bool
     */
    public function isSizeManual(): bool
    {
        return $this->sizeManual;
    }

    /**
     * @param bool $sizeManual
     *
     * @return $this
     */
    public function setSizeManual(bool $sizeManual = true)
    {
        $this->sizeManual = $sizeManual;

        return $this;
    }
}