<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Form\Entity\Input;
use Leon\BswBundle\Module\Form\Entity\Select;

trait ChangeTriggerHide
{
    /**
     * @var array
     */
    protected $changeTriggerHide = [];

    /**
     * @return array
     */
    public function getChangeTriggerHide(): array
    {
        if (empty($this->changeTriggerHide)) {
            return $this->changeTriggerHide;
        }

        if (method_exists($this, 'setAllowClear')) {
            $this->setAllowClear(false);
        }

        if (method_exists($this, 'setChange')) {
            if (static::class == Input::class) {
                $this->setChange('changeTriggerHideForInput');
            } elseif (static::class == Select::class) {
                $this->setChange('changeTriggerHideForSelect');
            } else {
                $this->setChange('changeTriggerHide');
            }
        }

        foreach ($this->changeTriggerHide as &$item) {
            $item = array_map('strval', (array)$item);
        }

        return $this->changeTriggerHide;
    }

    /**
     * @param array $changeTriggerHide
     *
     * @return $this
     */
    public function setChangeTriggerHide(array $changeTriggerHide)
    {
        $this->changeTriggerHide = $changeTriggerHide;

        return $this;
    }

    /**
     * @param array $changeTriggerHide
     *
     * @return $this
     */
    public function appendChangeTriggerHide(array $changeTriggerHide)
    {
        $this->changeTriggerHide = array_merge($this->changeTriggerHide, $changeTriggerHide);

        return $this;
    }
}