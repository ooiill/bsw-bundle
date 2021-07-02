<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Form\Form;

class Transfer extends Form
{
    use GetSetter\DataSource;
    use GetSetter\SourceTitle;
    use GetSetter\TargetTitle;
    use GetSetter\SourceOperate;
    use GetSetter\TargetOperate;
    use GetSetter\SelectedKeys;
    use GetSetter\SelectedKeysKey;
    use GetSetter\TargetKeys;
    use GetSetter\TargetKeysKey;
    use GetSetter\ShowSearch;
    use GetSetter\FilterOption;
    use GetSetter\ShowSelectAll;
    use GetSetter\ListStyle;

    /**
     * @const array Demo
     */
    const DATA_SOURCE_DEMO = [
        ['key' => 1001, 'title' => 'IT department', 'disabled' => true],
        ['key' => 1002, 'title' => 'DevOps department', 'disabled' => false],
        ['key' => 1003, 'title' => 'Product department', 'disabled' => false],
    ];

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->setFilterOption('bsw.filterOptionForTransfer');
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
        foreach ($options as $key => $title) {
            $optionsHandling[] = [
                'key'      => $key,
                'title'    => $title,
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