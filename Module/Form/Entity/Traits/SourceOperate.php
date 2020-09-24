<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait SourceOperate
{
    /**
     * @var string
     */
    protected $sourceOperate = '';

    /**
     * @return string
     */
    public function getSourceOperate(): string
    {
        return $this->sourceOperate;
    }

    /**
     * @param string $sourceOperate
     *
     * @return $this
     */
    public function setSourceOperate(string $sourceOperate)
    {
        $this->sourceOperate = $sourceOperate;

        return $this;
    }
}