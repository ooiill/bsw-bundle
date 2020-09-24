<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Flag
{
    /**
     * @var string
     */
    protected $flag = 'file';

    /**
     * @return string
     */
    public function getFlag(): string
    {
        return $this->flag;
    }

    /**
     * @param string $flag
     *
     * @return $this
     */
    public function setFlag(string $flag)
    {
        $this->flag = $flag;

        return $this;
    }
}