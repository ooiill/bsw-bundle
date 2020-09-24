<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait SourceTitle
{
    /**
     * @var string
     */
    protected $sourceTitle = 'Source';

    /**
     * @return string
     */
    public function getSourceTitle(): string
    {
        return $this->sourceTitle ?? '';
    }

    /**
     * @param string|null $sourceTitle
     *
     * @return $this
     */
    public function setSourceTitle(?string $sourceTitle = null)
    {
        $this->sourceTitle = $sourceTitle;

        return $this;
    }
}