<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SelectedMode
{
    /**
     * @var string
     */
    protected $selectedMode;

    /**
     * @return string
     */
    public function getSelectedMode(): string
    {
        return $this->selectedMode;
    }

    /**
     * @param string $selectedMode
     *
     * @return $this
     */
    public function setSelectedMode(string $selectedMode)
    {
        $this->selectedMode = $selectedMode;

        return $this;
    }
}