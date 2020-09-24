<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait Rows
{
    /**
     * @var int
     */
    protected $rows = 3;

    /**
     * @return int
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * @param int $rows
     *
     * @return $this
     */
    public function setRows(int $rows)
    {
        $this->rows = $rows;

        return $this;
    }
}