<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait ButtonType
{
    /**
     * @var string
     */
    protected $buttonType = Abs::THEME_DASHED;

    /**
     * @return string
     */
    public function getButtonType(): string
    {
        return $this->buttonType;
    }

    /**
     * @param string $buttonType
     *
     * @return $this
     */
    public function setButtonType(string $buttonType)
    {
        $this->buttonType = $buttonType;

        return $this;
    }
}