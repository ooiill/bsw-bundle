<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MaxRows
{
    /**
     * @var int
     */
    protected $maxRows = 10;

    /**
     * @return int
     */
    public function getMaxRows(): int
    {
        return $this->maxRows;
    }

    /**
     * @param int $maxRows
     *
     * @return $this
     */
    public function setMaxRows(int $maxRows)
    {
        $this->maxRows = $maxRows;

        return $this;
    }
}