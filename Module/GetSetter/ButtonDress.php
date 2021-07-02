<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait ButtonDress
{
    /**
     * @var string
     */
    protected $buttonDress = Abs::BUTTON_SOLID;

    /**
     * @return string
     */
    public function getButtonDress(): ?string
    {
        return $this->buttonDress;
    }

    /**
     * @param string $buttonDress
     *
     * @return $this
     */
    public function setButtonDress(string $buttonDress)
    {
        $this->buttonDress = $buttonDress;

        return $this;
    }
}