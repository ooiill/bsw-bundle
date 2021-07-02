<?php

namespace Leon\BswBundle\Module\GetSetter;

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