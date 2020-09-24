<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Search
{
    /**
     * @var string
     */
    protected $search;

    /**
     * @return string
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string $search
     *
     * @return $this
     */
    public function setSearch(string $search = null)
    {
        $this->search = $search;

        return $this;
    }
}