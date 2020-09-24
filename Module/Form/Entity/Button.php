<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\BindLoading;
use Leon\BswBundle\Module\Form\Entity\Traits\BindVariable;
use Leon\BswBundle\Module\Form\Entity\Traits\Block;
use Leon\BswBundle\Module\Form\Entity\Traits\Shape;
use Leon\BswBundle\Module\Form\Entity\Traits\Ghost;
use Leon\BswBundle\Module\Form\Entity\Traits\HtmlType;
use Leon\BswBundle\Module\Form\Entity\Traits\Scene;
use Leon\BswBundle\Module\Form\Entity\Traits\Selector;
use Leon\BswBundle\Module\Form\Entity\Traits\Size;
use Leon\BswBundle\Module\Form\Entity\Traits\Type;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForSelector;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Traits\Link;

class Button extends Form
{
    use Size;
    use Scene;
    use Block;
    use Ghost;
    use Shape;
    use Selector;
    use Type;
    use HtmlType;
    use BindVariable;
    use BindLoading;
    use Link;
    use VarNameForSelector;

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