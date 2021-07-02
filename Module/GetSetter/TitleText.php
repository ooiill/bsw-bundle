<?php

namespace Leon\BswBundle\Module\GetSetter;

trait TitleText
{
    /**
     * @var string
     */
    protected $titleText;

    /**
     * @return string
     */
    public function getTitleText(): ?string
    {
        return $this->titleText;
    }

    /**
     * @param string $titleText
     *
     * @return $this
     */
    public function setTitleText(string $titleText)
    {
        $this->titleText = $titleText;

        return $this;
    }
}