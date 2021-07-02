<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait DataSource
{
    /**
     * @var array
     */
    protected $dataSource = [];

    /**
     * @return string
     */
    public function getDataSource(): string
    {
        return Helper::jsonStringify($this->getDataSourceArray());
    }

    /**
     * @return array
     */
    public function getDataSourceArray(): array
    {
        return Helper::stringValues($this->dataSource);
    }

    /**
     * @param array $dataSource
     *
     * @return $this
     */
    public function setDataSource(array $dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }
}