<?php

namespace Leon\BswBundle\Module\GetSetter;

trait OptionFilterProp
{
    /**
     * @var string
     */
    protected $optionFilterProp;

    /**
     * @return string
     */
    public function getOptionFilterProp(): string
    {
        return $this->optionFilterProp;
    }

    /**
     * @param string $optionFilterProp
     *
     * @return $this
     */
    public function setOptionFilterProp(string $optionFilterProp)
    {
        $this->optionFilterProp = $optionFilterProp;

        return $this;
    }
}