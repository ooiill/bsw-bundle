<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Height
{
    /**
     * @var string
     */
    protected $height = '560px';

    /**
     * @var string
     */
    protected $heightFixedMobile = '400px';

    /**
     * @return string
     */
    public function getHeight(): string
    {
        return $this->isMobile() ? $this->heightFixedMobile : $this->height;
    }

    /**
     * @param string $height
     *
     * @return $this
     */
    public function setHeight(string $height)
    {
        $this->height = $height;

        return $this;
    }
}