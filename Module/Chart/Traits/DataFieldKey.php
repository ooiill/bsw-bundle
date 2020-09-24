<?php

namespace Leon\BswBundle\Module\Chart\Traits;

trait DataFieldKey
{
    /**
     * @var string
     */
    protected $dataFieldKey;

    /**
     * @return string
     */
    public function getDataFieldKey(): ?string
    {
        return $this->dataFieldKey;
    }

    /**
     * @param string $dataFieldKey
     *
     * @return $this
     */
    public function setDataFieldKey(string $dataFieldKey)
    {
        $this->dataFieldKey = $dataFieldKey;

        return $this;
    }
}