<?php

namespace Leon\BswBundle\Module\GetSetter;

trait SelectedKeysKey
{
    /**
     * @var string
     */
    protected $selectedKeysKey;

    /**
     * @return string
     */
    public function getSelectedKeysKey(): ?string
    {
        return $this->selectedKeysKey;
    }

    /**
     * @param string $selectedKeysKey
     *
     * @return $this
     */
    public function setSelectedKeysKey(string $selectedKeysKey)
    {
        $this->selectedKeysKey = $selectedKeysKey;

        return $this;
    }
}