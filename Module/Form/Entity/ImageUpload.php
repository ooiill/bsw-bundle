<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Component\Helper;

class ImageUpload extends Upload
{
    use GetSetter\MaxWidth;
    use GetSetter\MaxHeight;

    /**
     * ImageUpload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMaxWidth(200);
        $this->setMaxHeight(200);
    }

    /**
     * @return string
     */
    public function getKeyForInit(): string
    {
        return Helper::camelToUnder($this->key, '-');
    }
}
