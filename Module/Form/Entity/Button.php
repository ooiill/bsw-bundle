<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Scene\Link;

class Button extends Form
{
    use Link;
    use GetSetter\Size;
    use GetSetter\Scene;
    use GetSetter\Block;
    use GetSetter\Ghost;
    use GetSetter\Shape;
    use GetSetter\Selector;
    use GetSetter\Type;
    use GetSetter\HtmlType;
    use GetSetter\BindVariable;
    use GetSetter\BindLoading;
    use GetSetter\VarNameForSelector;

    /**
     * Button constructor.
     *
     * @param string|null $label
     * @param string|null $route
     * @param string|null $icon
     * @param string|null $type
     */
    public function __construct(string $label = null, string $route = null, string $icon = null, string $type = null)
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
        $this->setType($type ?? Abs::THEME_PRIMARY);
        $this->setHtmlType(Abs::TYPE_BUTTON);

        isset($label) && $this->setLabel($label);
        isset($route) && $this->setRoute($route);
        isset($icon) && $this->setIcon($icon);

        $this->setVarNameForSelector('previewSelectedRow');
    }
}