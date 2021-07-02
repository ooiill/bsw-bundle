<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait FormData
{
    /**
     * @var array
     */
    protected $formData = [];

    /**
     * @return string
     */
    public function getFormData(): string
    {
        return Helper::jsonStringify($this->formData);
    }

    /**
     * @return array
     */
    public function getFormDataArray(): array
    {
        return $this->formData;
    }

    /**
     * @param array $formData
     *
     * @return $this
     */
    public function setFormData(array $formData)
    {
        $this->formData = $formData;

        return $this;
    }

    /**
     * @param array $formData
     *
     * @return $this
     */
    public function appendFormData(array $formData)
    {
        $this->formData = array_merge($this->formData, $formData);

        return $this;
    }
}