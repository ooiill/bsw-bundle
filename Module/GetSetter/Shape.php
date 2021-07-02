<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Shape
{
    /**
     * @var string
     */
    protected $shape;

    /**
     * @return string
     */
    public function getShape(): ?string
    {
        return $this->shape;
    }

    /**
     * @param string $shape
     *
     * @return $this
     */
    public function setShape(string $shape)
    {
        $this->shape = $shape;

        return $this;
    }
}