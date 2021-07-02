<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Width
{
    /**
     * @var string
     */
    protected $width = '99.6%';

    /**
     * @var string
     */
    protected $widthFixedMobile = '100%';

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->isMobile() ? $this->widthFixedMobile : $this->width;
    }

    /**
     * @param string $width
     *
     * @return $this
     */
    public function setWidth(string $width)
    {
        $this->width = $width;

        return $this;
    }
}