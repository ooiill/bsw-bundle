<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Form\Form;

class AutoComplete extends Form
{
    use GetSetter\AllowClear;
    use GetSetter\BackFill;
    use GetSetter\DropdownStyle;
    use GetSetter\DataSource;
    use GetSetter\FilterOption;
    use GetSetter\Search;
    use GetSetter\Size;

    /**
     * @const array Demo
     */
    const DATA_SOURCE_DEMO = [
        ['value' => 1001, 'text' => 'IT department'],
        ['value' => 1002, 'text' => 'DevOps department'],
        ['value' => 1003, 'text' => 'Product department'],
    ];

    /**
     * AutoComplete constructor.
     */
    public function __construct()
    {
        $this->setAllowClear(false);
        $this->setFilterOption('bsw.filterOptionForAutoComplete');
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
        foreach ($options as $value => $text) {
            $optionsHandling[] = [
                'value' => $value,
                'text'  => $text,
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
        return $this->setDataSource($this->enumHandler($options));
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->getDataSourceArray();
    }
}