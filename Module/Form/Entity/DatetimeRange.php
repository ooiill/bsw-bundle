<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;

class DatetimeRange extends Datetime
{
    use GetSetter\TimeFormat;
    use GetSetter\TimeBoundary;
    use GetSetter\Separator;

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'range-picker';
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        if (is_string($this->value)) {
            $this->value = explode(Abs::FORM_DATA_SPLIT, $this->value);
        }

        if (is_array($this->value) && count($this->value) >= 2) {
            return [trim($this->value[0]), trim($this->value[1])];
        }

        return [null, null];
    }
}