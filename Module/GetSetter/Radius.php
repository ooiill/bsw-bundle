<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Radius
{
    /**
     * @var array
     */
    protected $radius = ['40%', '70%'];

    /**
     * @return array
     */
    public function getRadius(): array
    {
        return $this->radius;
    }

    /**
     * @param array $radius
     *
     * @return $this
     */
    public function setRadius(array $radius)
    {
        $this->radius = $radius;

        return $this;
    }
}