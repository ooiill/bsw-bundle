<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class SelectTree extends Form
{
    use GetSetter\Size;
    use GetSetter\AllowClear;
    use GetSetter\LabelInValue;
    use GetSetter\ShowSearch;
    use GetSetter\ShowArrow;
    use GetSetter\ShowCheckedStrategy;
    use GetSetter\OptionFilterProp;
    use GetSetter\DropdownStyle;
    use GetSetter\DropdownEqualWidth;
    use GetSetter\TreeData;
    use GetSetter\ExpandAll;

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