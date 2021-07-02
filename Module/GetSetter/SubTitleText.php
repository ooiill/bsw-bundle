<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SubTitleText
{
    /**
     * @var string
     */
    protected $subTitleText;

    /**
     * @return string
     */
    public function getSubTitleText(): ?string
    {
        return $this->subTitleText;
    }

    /**
     * @param string $subTitleText
     *
     * @return $this
     */
    public function setSubTitleText(string $subTitleText)
    {
        $this->subTitleText = $subTitleText;

        return $this;
    }
}