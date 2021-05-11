<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownEqualWidth;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\ExpandAll;
use Leon\BswBundle\Module\Form\Entity\Traits\LabelInValue;
use Leon\BswBundle\Module\Form\Entity\Traits\OptionFilterProp;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowArrow;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowCheckedStrategy;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\TreeData;
use Leon\BswBundle\Module\Form\Form;

class SelectTree extends Form
{
    use Size;
    use AllowClear;
    use LabelInValue;
    use ShowSearch;
    use ShowArrow;
    use ShowCheckedStrategy;
    use OptionFilterProp;
    use DropdownStyle;
    use DropdownEqualWidth;
    use TreeData;
    use ExpandAll;

    /**
     * @const array demo
     */
    const TREE_DATA_DEMO = [
        [
            'value'    => '1',
            'title'    => 'IT department',
            'disabled' => false,
            'children' => [
                [
                    'value'    => '1-1',
                    'title'    => 'Backend',
                    'disabled' => false,
                    'children' => [
                        ['value' => '1-1-1', 'title' => 'php', 'disabled' => false],
                        ['value' => '1-1-2', 'title' => 'go', 'disabled' => false],
                        ['value' => '1-1-3', 'title' => 'python', 'disabled' => false],
                    ],
                ],
                ['value' => '1-2', 'title' => 'Frontend', 'disabled' => false],
                ['value' => '1-3', 'title' => 'Client', 'disabled' => false],
            ],
        ],
        ['value' => '2', 'title' => 'DevOps department', 'disabled' => false],
        ['value' => '3', 'title' => 'Product department', 'disabled' => false],
    ];

    /**
     * Select constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setOptionFilterProp(Abs::SEARCH_TITLE);
        $this->setShowCheckedStrategy(Abs::CHECKED_STRATEGY_ALL);
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
        foreach ($options as $value => $title) {
            $optionsHandling[] = [
                'value'    => $value,
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
        return $this->setTreeData($this->enumHandler($options));
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->getTreeDataArray();
    }
}