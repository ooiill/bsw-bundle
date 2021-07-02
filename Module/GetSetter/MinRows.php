<?php

namespace Leon\BswBundle\Module\GetSetter;

trait MinRows
{
    /**
     * @var int
     */
    protected $minRows = 4;

    /**
     * @return int
     */
    public function getMinRows(): int
    {
        return $this->minRows;
    }

    /**
     * @param int $minRows
     *
     * @return $this
     */
    public function setMinRows(int $minRows)
    {
        $this->minRows = $minRows;

        return $this;
    }
}