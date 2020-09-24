<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Character
{
    /**
     * @var string
     */
    protected $character;

    /**
     * @return string
     */
    public function getCharacter(): ?string
    {
        return $this->character;
    }

    /**
     * @param string $character
     *
     * @return $this
     */
    public function setCharacter(string $character)
    {
        $this->character = $character;

        return $this;
    }
}