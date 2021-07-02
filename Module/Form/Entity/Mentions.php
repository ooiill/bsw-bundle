<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class Mentions extends Form
{
    use GetSetter\Options;
    use GetSetter\FilterOption;
    use GetSetter\Placement;
    use GetSetter\Prefix;
    use GetSetter\Separator;
    use GetSetter\Rows;
    use GetSetter\ValueTpl;

    /**
     * @const array Demo
     */
    const OPTIONS_DEMO = [
        ['value' => 1001, 'label' => 'IT department'],
        ['value' => 1002, 'label' => 'DevOps department'],
        ['value' => 1003, 'label' => 'Product department'],
    ];

    /**
     * Mentions constructor.
     */
    public function __construct()
    {
        $this->setPlacement(Abs::POS_BOTTOM);
        $this->setPrefix('@');
        $this->setValueTpl('{$label}!{$value}');
        $this->setSeparator(' ');
        $this->setFilterOption('');
        $this->setFilterOption('bsw.filterOptionForMentions');
    }

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
                'value' => $value,
                'label' => $label,
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