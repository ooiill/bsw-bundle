<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Form\Entity\Input;
use Leon\BswBundle\Module\Form\Entity\Select;

trait ChangeTriggerDisabled
{
    /**
     * @var array
     */
    protected $changeTriggerDisabled = [];

    /**
     * @return array
     */
    public function getChangeTriggerDisabled(): array
    {
        if (empty($this->changeTriggerDisabled)) {
            return $this->changeTriggerDisabled;
        }

        if (method_exists($this, 'setAllowClear')) {
            $this->setAllowClear(false);
        }

        if (method_exists($this, 'setChange')) {
            if (static::class == Input::class) {
                $this->setChange('changeTriggerDisabledForInput');
            } elseif (static::class == Select::class) {
                $this->setChange('changeTriggerDisabledForSelect');
            } else {
                $this->setChange('changeTriggerDisabled');
            }
        }

        foreach ($this->changeTriggerDisabled as &$item) {
            $item = array_map('strval', (array)$item);
        }

        return $this->changeTriggerDisabled;
    }

    /**
     * @param array $changeTriggerDisabled
     *
     * @return $this
     */
    public function setChangeTriggerDisabled(array $changeTriggerDisabled)
    {
        $this->changeTriggerDisabled = $changeTriggerDisabled;

        return $this;
    }

    /**
     * @param array $changeTriggerDisabled
     *
     * @return $this
     */
    public function appendChangeTriggerDisabled(array $changeTriggerDisabled)
    {
        $this->changeTriggerDisabled = array_merge($this->changeTriggerDisabled, $changeTriggerDisabled);

        return $this;
    }
}