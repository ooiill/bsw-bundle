<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Form\Form;

class Checkbox extends Form
{
    use GetSetter\Options;
    use GetSetter\Col;

    /**
     * @const array Demo
     */
    const OPTIONS_DEMO = [
        ['value' => 1001, 'label' => 'IT department', 'disabled' => false],
        ['value' => 1002, 'label' => 'DevOps department', 'disabled' => true],
        ['value' => 1003, 'label' => 'Product department', 'disabled' => false],
    ];

    /**
     * @param array $options
     *
     * @return array
     */
    public function enumHandler(array $options): array
    {
        if (!is_scalar(current($options))) {
            return $options;
        }

        $optionsHandling = [];
        foreach ($options as $value => $label) {
            $optionsHandling[] = [
                'value'    => $value,
                'label'    => $label,
                'disabled' => false,
            ];
        }

        return $optionsHandling;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setEnum(array $options)
    {
        return $this->setOptions($this->enumHandler($options));
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->getOptionsArray();
    }
}