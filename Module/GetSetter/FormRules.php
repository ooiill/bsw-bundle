<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait FormRules
{
    /**
     * @var array
     */
    protected $formRules = [];

    /**
     * @return array
     */
    public function getFormRulesArray(): array
    {
        return $this->formRules;
    }

    /**
     * @return string
     */
    public function getFormRules(): string
    {
        return Helper::jsonStringify($this->formRules);
    }

    /**
     * @param array $formRules
     *
     * @return $this
     */
    public function setFormRules(array $formRules)
    {
        $this->formRules = $formRules;

        return $this;
    }

    /**
     * @param array $formRules
     *
     * @return $this
     */
    public function appendFormRules(array $formRules)
    {
        $this->formRules = array_merge($this->formRules, $formRules);

        return $this;
    }
}