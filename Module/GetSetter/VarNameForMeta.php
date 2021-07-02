<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait VarNameForMeta
{
    /**
     * @var string|bool
     */
    protected $varNameForMeta = false;

    /**
     * @var array
     */
    protected $varNameForMetaDefault = [];

    /**
     * @return string|bool
     */
    public function getVarNameForMeta()
    {
        return $this->varNameForMeta;
    }

    /**
     * @param string|bool $varNameForMeta
     *
     * @return $this
     */
    public function setVarNameForMeta($varNameForMeta)
    {
        $this->varNameForMeta = $varNameForMeta;

        return $this;
    }

    /**
     * @return string
     */
    public function getVarNameForMetaDefault(): string
    {
        return Helper::jsonStringify($this->getVarNameForMetaDefaultArray());
    }

    /**
     * @return array
     */
    public function getVarNameForMetaDefaultArray(): array
    {
        return Helper::stringValues($this->varNameForMetaDefault);
    }

    /**
     * @param array $varNameForMetaDefault
     *
     * @return $this
     */
    public function setVarNameForMetaDefault(array $varNameForMetaDefault)
    {
        $this->varNameForMetaDefault = $varNameForMetaDefault;

        return $this;
    }
}