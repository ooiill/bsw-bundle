<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait FormScene
{
    /**
     * @var array
     */
    protected $formScene = [
        Abs::TAG_FILTER      => false,
        Abs::TAG_PERSISTENCE => true,
    ];

    /**
     * @param string ...$names
     *
     * @return $this
     */
    public function formSceneDisable(string ...$names)
    {
        foreach ($names as $name) {
            $this->formScene[$name] = false;
        }

        return $this;
    }

    /**
     * @param string ...$names
     *
     * @return $this
     */
    public function formSceneEnable(string ...$names)
    {
        foreach ($names as $name) {
            $this->formScene[$name] = true;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function formSceneState(string $name): bool
    {
        return $this->formScene[$name] ?? false;
    }
}