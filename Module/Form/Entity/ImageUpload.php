<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits\MaxHeight;
use Leon\BswBundle\Module\Form\Entity\Traits\MaxWidth;

class ImageUpload extends Upload
{
    use MaxWidth;
    use MaxHeight;

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
