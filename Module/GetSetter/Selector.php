<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait Selector
{
    /**
     * @var string
     */
    protected $selector;

    /**
     * @return string
     */
    public function getSelector(): ?string
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function setSelector(string $selector)
    {
        if (in_array($selector, [Abs::SELECTOR_CHECKBOX, Abs::SELECTOR_RADIO])) {
            $this->selector = $selector;
        }

        return $this;
    }
}