<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait MapSource
{
    /**
     * @var string
     */
    protected $mapJsonFile;

    /**
     * @var string
     */
    protected $mapKey;

    /**
     * @return string
     */
    public function getMapJsonFile(): string
    {
        return $this->mapJsonFile;
    }

    /**
     * @param string $mapJsonFile
     *
     * @return $this
     */
    public function setMapJsonFile(string $mapJsonFile)
    {
        $this->mapJsonFile = $mapJsonFile;

        return $this;
    }

    /**
     * @param string $mapJsonFileQuick
     *
     * @return $this
     */
    public function setMapJsonFileQuick(string $mapJsonFileQuick)
    {
        // world、china、china-cities、china-contour、province/xx
        $format = '/bundles/leonbsw/node_modules/echarts/map/json/%s.json';
        $mapJsonFile = sprintf($format, trim($mapJsonFileQuick, '/'));
        $this->mapJsonFile = $mapJsonFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getMapKey(): string
    {
        if (isset($this->mapKey)) {
            return $this->mapKey;
        }

        if (!isset($this->mapJsonFile)) {
            return 'un-configure';
        }

        return Helper::cutString($this->mapJsonFile, ['/^-1', '.^0']);
    }

    /**
     * @param string $mapKey
     *
     * @return $this
     */
    public function setMapKey(string $mapKey)
    {
        $this->mapKey = $mapKey;

        return $this;
    }
}