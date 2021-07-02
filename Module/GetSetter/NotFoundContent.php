<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait NotFoundContent
{
    /**
     * @var string
     */
    protected $notFoundContent = Abs::NIL;

    /**
     * @return string
     */
    public function getNotFoundContent(): string
    {
        return $this->notFoundContent;
    }

    /**
     * @param string $notFoundContent
     *
     * @return $this
     */
    public function setNotFoundContent(string $notFoundContent)
    {
        $this->notFoundContent = $notFoundContent;

        return $this;
    }
}