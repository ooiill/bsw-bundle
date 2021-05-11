<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Traits\Gutter;
use Leon\BswBundle\Module\Form\Entity\Traits\MemberKeyAuto;
use Leon\BswBundle\Module\Form\Entity\Traits\Responsive;
use Leon\BswBundle\Module\Form\Form;

class Group extends Form
{
    use Responsive;
    use Gutter;
    use MemberKeyAuto;

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
        if ($this->getMemberKeyAuto()) {
            $this->setMemberKeyAuto(false);
            foreach ($this->member as $key => $item) {
                $item->setKey($this->getKey() . '_' . ($item->getKey() ?? $key));
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
     *
     * @return $this
     */
    public function setColumnSingle($index, int $column)
    {
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