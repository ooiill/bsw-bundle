<?php

namespace Leon\BswBundle\Module\Form;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\GetSetter;

abstract class Form
{
    use GetSetter\Key;
    use GetSetter\Field;
    use GetSetter\Name;
    use GetSetter\Label;
    use GetSetter\Value;
    use GetSetter\ValueShadow;
    use GetSetter\ClassCss;
    use GetSetter\Attributes;
    use GetSetter\Disabled;
    use GetSetter\DisabledOverall;
    use GetSetter\Style;
    use GetSetter\ParentStyle;
    use GetSetter\Placeholder;
    use GetSetter\FormRules;
    use GetSetter\Change;
    use GetSetter\AutoFocus;
    use GetSetter\FormData;
    use GetSetter\ButtonType;
    use GetSetter\ButtonBlock;
    use GetSetter\ButtonStyle;
    use GetSetter\FormScene;
    use GetSetter\Area;
    use GetSetter\ChangeTriggerHide;
    use GetSetter\ChangeTriggerDisabled;
    use GetSetter\VarNameForMeta;
    use GetSetter\VarNameForMetaField;
    use GetSetter\VarNameForChange;
    use GetSetter\DoLogicRoute;
    use GetSetter\VModel;
    use GetSetter\DynamicRow;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return Helper::camelToUnder(Helper::clsName(static::class), '-');
    }
}
