<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Gutter
{
    /**
     * @var int|array
     */
    protected $gutter = 8;

    /**
     * @return int|string
     */
    public function getGutter()
    {
        if (is_int($this->gutter)) {
            return $this->gutter;
        }

        return Helper::jsonStringify($this->gutter);
    }

    /**
     * @param array|int $gutter
     *
     * @return $this
     */
    public function setGutter($gutter)
    {
        $this->gutter = $gutter;

        return $this;
    }
}