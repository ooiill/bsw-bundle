<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait FilterOption
{
    /**
     * @var string
     */
    protected $filterOption;

    /**
     * @return string
     */
    public function getFilterOption(): ?string
    {
        return $this->filterOption;
    }

    /**
     * @param string $filterOption
     *
     * @return $this
     */
    public function setFilterOption(string $filterOption)
    {
        $this->filterOption = $filterOption;

        return $this;
    }
}