<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait Scene
{
    /**
     * @var string
     */
    protected $scene = Abs::SCENE_NORMAL;

    /**
     * @return string
     */
    public function getScene(): string
    {
        return $this->scene;
    }

    /**
     * @param string $scene
     *
     * @return $this
     */
    public function setScene(string $scene)
    {
        $this->scene = $scene;

        return $this;
    }
}