<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait DynamicRow
{
    /**
     * @var bool
     */
    protected $dynamicRow = false;

    /**
     * @var string
     */
    protected $dynamicRowAdd;

    /**
     * @var string
     */
    protected $dynamicRowSub;

    /**
     * @var string
     */
    protected $dynamicRowLabel = 'Add field';

    /**
     * @return bool
     */
    public function isDynamicRow(): bool
    {
        return $this->dynamicRow;
    }

    /**
     * @param bool $dynamicRow
     *
     * @return $this
     */
    public function setDynamicRow(bool $dynamicRow)
    {
        $this->dynamicRow = $dynamicRow;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowAdd(): ?string
    {
        return $this->dynamicRowAdd;
    }

    /**
     * @param string $dynamicRowAdd
     *
     * @return $this
     */
    public function setDynamicRowAdd(string $dynamicRowAdd)
    {
        $this->dynamicRowAdd = $dynamicRowAdd;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowSub(): ?string
    {
        return $this->dynamicRowSub;
    }

    /**
     * @param string $dynamicRowSub
     *
     * @return $this
     */
    public function setDynamicRowSub(string $dynamicRowSub)
    {
        $this->dynamicRowSub = $dynamicRowSub;

        return $this;
    }

    /**
     * @return string
     */
    public function getDynamicRowLabel(): string
    {
        return $this->dynamicRowLabel;
    }

    /**
     * @param string $dynamicRowLabel
     *
     * @return $this
     */
    public function setDynamicRowLabel(string $dynamicRowLabel)
    {
        $this->dynamicRowLabel = $dynamicRowLabel;

        return $this;
    }
}