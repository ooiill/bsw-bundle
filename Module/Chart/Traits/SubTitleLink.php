<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait SubTitleLink
{
    /**
     * @var string
     */
    protected $subTitleLink;

    /**
     * @return string
     */
    public function getSubTitleLink(): ?string
    {
        return $this->subTitleLink;
    }

    /**
     * @param string $subTitleLink
     *
     * @return $this
     */
    public function setSubTitleLink(string $subTitleLink)
    {
        $this->subTitleLink = $subTitleLink;

        return $this;
    }
}