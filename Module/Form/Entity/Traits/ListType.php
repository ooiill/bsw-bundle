<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait ListType
{
    /**
     * @var string
     */
    protected $listType = Abs::LIST_TYPE_TEXT;

    /**
     * @return string
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * @param string $listType
     *
     * @return $this
     */
    public function setListType(string $listType)
    {
        $this->listType = $listType;

        return $this;
    }
}