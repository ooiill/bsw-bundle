<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\Separator;
use Leon\BswBundle\Module\Form\Entity\Traits\TimeBoundary;
use Leon\BswBundle\Module\Form\Entity\Traits\TimeFormat;

class DatetimeRange extends Datetime
{
    use TimeFormat;
    use TimeBoundary;
    use Separator;

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