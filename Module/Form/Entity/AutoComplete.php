<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\BackFill;
use Leon\BswBundle\Module\Form\Entity\Traits\DataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\DynamicDataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\FilterOption;
use Leon\BswBundle\Module\Form\Entity\Traits\Search;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForMeta;
use Leon\BswBundle\Module\Form\Form;

class AutoComplete extends Form
{
    use AllowClear;
    use BackFill;
    use DropdownStyle;
    use DataSource;
    use VarNameForMeta;
    use DynamicDataSource;
    use FilterOption;
    use Search;
    use Size;

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