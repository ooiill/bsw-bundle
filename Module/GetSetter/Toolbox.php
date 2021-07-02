<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Toolbox
{
    /**
     * @var array
     */
    protected $toolbox = [];

    /**
     * @return array
     */
    public function getToolbox(): array
    {
        return $this->toolbox;
    }

    /**
     * @param array $toolbox
     *
     * @return $this
     */
    public function setToolbox(array $toolbox)
    {
        $this->toolbox = $toolbox;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setToolboxField(string $field, $value)
    {
        Helper::setArrayValue($this->toolbox, $field, $value);

        return $this;
    }
}