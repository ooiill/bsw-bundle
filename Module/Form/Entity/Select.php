<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\AllowClear;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownEqualWidth;
use Leon\BswBundle\Module\Form\Entity\Traits\DropdownStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\LabelInValue;
use Leon\BswBundle\Module\Form\Entity\Traits\Mode;
use Leon\BswBundle\Module\Form\Entity\Traits\NotFoundContent;
use Leon\BswBundle\Module\Form\Entity\Traits\OptionFilterProp;
use Leon\BswBundle\Module\Form\Entity\Traits\Options;
use Leon\BswBundle\Module\Form\Entity\Traits\PreviewRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\Search;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowArrow;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowSearch;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\TokenSeparators;
use Leon\BswBundle\Module\Form\Entity\Traits\UseOptions;
use Leon\BswBundle\Module\Form\Form;

class Select extends Form
{
    use Size;
    use Options;
    use PreviewRoute;
    use AllowClear;
    use ButtonLabel;
    use NotFoundContent;
    use LabelInValue;
    use Mode;
    use ShowSearch;
    use ShowArrow;
    use OptionFilterProp;
    use TokenSeparators;
    use DropdownStyle;
    use DropdownEqualWidth;
    use Search;
    use UseOptions;

    /**
     * @const array Demo
     */
    const OPTIONS_DEMO = [
        ['value' => 1001, 'label' => 'IT department', 'disabled' => false],
        ['value' => 1002, 'label' => 'DevOps department', 'disabled' => true],
        ['value' => 1003, 'label' => 'Product department', 'disabled' => false],
    ];

    /**
     * Select constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setButtonLabel('Popup for select');
        $this->setMode(Abs::MODE_DEFAULT);
        $this->setOptionFilterProp(Abs::SEARCH_LABEL);
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

    /**
     * @return bool
     */
    public function isValueMultiple(): bool
    {
        return is_array($this->value) || is_object($this->value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (is_array($this->value)) {
            return Helper::jsonStringify(array_map('strval', $this->value));
        }

        return $this->value;
    }
}