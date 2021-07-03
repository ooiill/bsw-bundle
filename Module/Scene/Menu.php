<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Scene\Link;

class Menu
{
    use Link;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $menuId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getMenuId(): int
    {
        return $this->menuId;
    }

    /**
     * @param int $menuId
     *
     * @return $this
     */
    public function setMenuId(int $menuId)
    {
        $this->menuId = $menuId;

        return $this;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     *
     * @return $this
     * @throws
     */
    public function attributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            if (!property_exists($this, $name)) {
                throw new ModuleException(static::class . " has no property named `{$name}`");
            }
            $this->{$name} = $value;
        }

        return $this;
    }
}