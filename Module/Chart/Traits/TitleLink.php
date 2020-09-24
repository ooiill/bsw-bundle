<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait TitleLink
{
    /**
     * @var string
     */
    protected $titleLink;

    /**
     * @return string
     */
    public function getTitleLink(): ?string
    {
        return $this->titleLink;
    }

    /**
     * @param string $titleLink
     *
     * @return $this
     */
    public function setTitleLink(string $titleLink)
    {
        $this->titleLink = $titleLink;

        return $this;
    }
}