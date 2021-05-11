<?php

namespace Leon\BswBundle\Module\Form;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits\Area;
use Leon\BswBundle\Module\Form\Entity\Traits\Attributes;
use Leon\BswBundle\Module\Form\Entity\Traits\AutoFocus;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonBlock;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonType;
use Leon\BswBundle\Module\Form\Entity\Traits\Change;
use Leon\BswBundle\Module\Form\Entity\Traits\ChangeTriggerDisabled;
use Leon\BswBundle\Module\Form\Entity\Traits\ChangeTriggerHide;
use Leon\BswBundle\Module\Form\Entity\Traits\ClassCss;
use Leon\BswBundle\Module\Form\Entity\Traits\Disabled;
use Leon\BswBundle\Module\Form\Entity\Traits\DisabledOverall;
use Leon\BswBundle\Module\Form\Entity\Traits\DoLogicRoute;
use Leon\BswBundle\Module\Form\Entity\Traits\Field;
use Leon\BswBundle\Module\Form\Entity\Traits\FormData;
use Leon\BswBundle\Module\Form\Entity\Traits\FormScene;
use Leon\BswBundle\Module\Form\Entity\Traits\Key;
use Leon\BswBundle\Module\Form\Entity\Traits\Label;
use Leon\BswBundle\Module\Form\Entity\Traits\Name;
use Leon\BswBundle\Module\Form\Entity\Traits\ParentStyle;
use Leon\BswBundle\Module\Form\Entity\Traits\Placeholder;
use Leon\BswBundle\Module\Form\Entity\Traits\FormRules;
use Leon\BswBundle\Module\Form\Entity\Traits\Style;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForChange;
use Leon\BswBundle\Module\Form\Entity\Traits\Value;
use Leon\BswBundle\Module\Form\Entity\Traits\ValueShadow;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForMeta;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForMetaField;

abstract class Form
{
    use Key;
    use Field;
    use Name;
    use Label;
    use Value;
    use ValueShadow;
    use ClassCss;
    use Attributes;
    use Disabled;
    use DisabledOverall;
    use Style;
    use ParentStyle;
    use Placeholder;
    use FormRules;
    use Change;
    use AutoFocus;
    use FormData;
    use ButtonType;
    use ButtonBlock;
    use ButtonStyle;
    use FormScene;
    use Area;
    use ChangeTriggerHide;
    use ChangeTriggerDisabled;
    use VarNameForMeta;
    use VarNameForMetaField;
    use VarNameForChange;
    use DoLogicRoute;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return Helper::camelToUnder(Helper::clsName(static::class), '-');
    }
}
