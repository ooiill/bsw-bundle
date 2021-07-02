<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Icon
{
    /**
     * @var string
     */
    protected $icon;

    /**
     * @var array
     */
    protected $iconClass = [];

    /**
     * @var array
     */
    protected $iconAttribute = [];

    /**
     * @return string|null
     */
    public function getIconTag(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        $flag = 'a';
        if (strpos($this->icon, ':') !== false) {
            $flag = $this->icon[0];
        }

        return $flag;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon(?string $icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return array
     */
    public function getIconClass(): array
    {
        return $this->iconClass;
    }

    /**
     * @param array $iconClass
     *
     * @return $this
     */
    public function setIconClass(array $iconClass)
    {
        $this->iconClass = $iconClass;

        return $this;
    }

    /**
     * @return array
     */
    public function getIconAttribute(): array
    {
        return $this->iconAttribute;
    }

    /**
     * @param array $iconAttribute
     *
     * @return $this
     */
    public function setIconAttribute(array $iconAttribute)
    {
        $this->iconAttribute = $iconAttribute;

        return $this;
    }

    /**
     * @param array $iconAttribute
     *
     * @return $this
     */
    public function appendIconAttribute(array $iconAttribute)
    {
        $this->iconAttribute = array_merge($this->iconAttribute, $iconAttribute);

        return $this;
    }
}