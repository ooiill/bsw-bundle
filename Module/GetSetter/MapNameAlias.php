<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;

trait MapNameAlias
{
    /**
     * @var array
     */
    protected $mapNameAlias = [];

    /**
     * @return array
     */
    public function getMapNameAlias(): array
    {
        return $this->mapNameAlias;
    }

    /**
     * @param array $mapNameAlias
     *
     * @return $this
     */
    public function setMapNameAlias(array $mapNameAlias)
    {
        $this->mapNameAlias = $mapNameAlias;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setMapNameAliasField(string $field, $value)
    {
        Helper::setArrayValue($this->mapNameAlias, $field, $value);

        return $this;
    }
}