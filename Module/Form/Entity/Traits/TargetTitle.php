<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait TargetTitle
{
    /**
     * @var string
     */
    protected $targetTitle = 'Target';

    /**
     * @return string
     */
    public function getTargetTitle(): string
    {
        return $this->targetTitle ?? '';
    }

    /**
     * @param string|null $targetTitle
     *
     * @return $this
     */
    public function setTargetTitle(?string $targetTitle = null)
    {
        $this->targetTitle = $targetTitle;

        return $this;
    }
}