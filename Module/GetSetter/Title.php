<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Title
{
    /**
     * @var array
     */
    protected $title = [];

    /**
     * @return array
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array $title
     *
     * @return $this
     */
    public function setTitle(array $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function setTitleField(string $field, $value)
    {
        Helper::setArrayValue($this->title, $field, $value);

        return $this;
    }
}