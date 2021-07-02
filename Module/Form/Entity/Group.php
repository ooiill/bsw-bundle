<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class Group extends Form
{
    use GetSetter\Responsive;
    use GetSetter\Gutter;
    use GetSetter\ComplexKey;

    /**
     * @var Form[]
     */
    protected $member = [];

    /**
     * @var array
     */
    protected $column = [];

    /**
     * Group constructor.
     */
    public function __construct()
    {
        $this->formSceneEnable(Abs::TAG_FILTER);
    }

    /**
     * @return Form[]
     */
    public function getMember(): array
    {
        foreach ($this->member as $key => $item) {
            $subKey = $item->getKey() ?? $key;
            if (strpos($subKey, $this->getKey()) !== false) {
                continue;
            }
            if ($this->isComplexKey()) {
                $item->setKey($this->getKey() . Helper::underToCamel($subKey, false));
            } else {
                $item->setKey($subKey);
            }
        }

        return $this->member;
    }

    /**
     * @param Form[] $member
     *
     * @return $this
     */
    public function setMember(array $member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @param Form $member
     *
     * @return $this
     */
    public function pushMember(Form $member)
    {
        array_push($this->member, $member);

        return $this;
    }

    /**
     * @return array
     */
    public function getColumn(): array
    {
        $count = count($this->member);
        $keys = array_keys($this->member);

        $default = array_fill(0, $count, floor(24 / $count));
        $default = array_combine($keys, $default);

        if (empty($this->column)) {
            return $default;
        }

        if (count($this->column) != count($this->member)) {
            return $default;
        }

        if (array_sum($this->column) > 24) {
            return $default;
        }

        return array_combine($keys, $this->column);
    }

    /**
     * @param array $column
     *
     * @return $this
     */
    public function setColumn(array $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param mixed $index
     * @param int   $column
     * @param bool  $force
     *
     * @return $this
     */
    public function setColumnSingle($index, int $column, bool $force = false)
    {
        if (isset($this->column[$index]) && !$force) {
            return $this;
        }

        $this->column[$index] = $column;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return Group
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            return $this;
        }
        foreach ($this->member as $item) {
            if (isset($value[$item->getKey()])) {
                $item->setValue($value[$item->getKey()]);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        $values = [];
        foreach ($this->member as $item) {
            $values[$item->getKey()] = $item->getValue();
        }

        return $values;
    }
}