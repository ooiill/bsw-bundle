<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\DataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\DynamicDataSource;
use Leon\BswBundle\Module\Form\Entity\Traits\FilterOption;
use Leon\BswBundle\Module\Form\Entity\Traits\ListStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\SelectedKeys;
use Leon\BswBundle\Module\Form\Entity\Traits\SelectedKeysKey;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSelectAll;
use Leon\BswBundle\Module\Form\Entity\Traits\SourceOperate;
use Leon\BswBundle\Module\Form\Entity\Traits\SourceTitle;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetKeys;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetKeysKey;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetOperate;
use Leon\BswBundle\Module\Form\Entity\Traits\TargetTitle;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForMeta;
use Leon\BswBundle\Module\Form\Form;

class Transfer extends Form
{
    use DataSource;
    use VarNameForMeta;
    use DynamicDataSource;
    use SourceTitle;
    use TargetTitle;
    use SourceOperate;
    use TargetOperate;
    use SelectedKeys;
    use SelectedKeysKey;
    use TargetKeys;
    use TargetKeysKey;
    use ShowSearch;
    use FilterOption;
    use ShowSelectAll;
    use ListStyle;

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