<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Split
{
    /**
     * @var string
     */
    protected $split = ';';

    /**
     * @return string
     */
    public function getSplit(): ?string
    {
        return $this->split;
    }

    /**
     * @param string $split
     *
     * @return $this
     */
    public function setSplit(string $split)
    {
        $this->split = $split;

        return $this;
    }
}