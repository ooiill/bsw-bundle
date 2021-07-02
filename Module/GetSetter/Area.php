<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Module\Entity\Abs;

trait Area
{
    /**
     * @var array
     */
    protected $area = [];

    /**
     * @return array
     */
    public function getArea(): array
    {
        return $this->area;
    }

    /**
     * @param array $area
     *
     * @return $this
     */
    public function setArea(array $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @param array $area
     *
     * @return $this
     */
    public function appendArea(array $area)
    {
        $this->area = array_merge($this->area, $area);

        return $this;
    }

    /**
     * @param string $area
     *
     * @return $this
     */
    public function setOnlyArea(string $area)
    {
        $this->area = [$area];

        return $this;
    }

    /**
     * @param string $area
     *
     * @return $this
     */
    public function setAddArea(string $area)
    {
        array_push($this->area, $area);

        return $this;
    }

    /**
     * @param string $area
     *
     * @return $this
     */
    public function setDelArea(string $area)
    {
        $index = array_search($this->area, $area);
        if ($index !== false) {
            unset($this->area[$index]);
            $this->area = array_values($this->area);
        }

        return $this;
    }

    /**
     * @param string $area
     *
     * @return bool
     */
    public function getAllowArea(string $area): bool
    {
        if (empty($this->area)) {
            return true;
        }

        return in_array($area, $this->area);
    }
}