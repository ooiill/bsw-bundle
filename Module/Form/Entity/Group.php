<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Form;

class Group extends Form
{
    /**
     * @var Form[]
     */
    protected $member = [];

    /**
     * @var bool
     */
    private $memberKeyAuto = true;

    /**
     * @var array
     */
    protected $column = [];

    /**
     * @var bool
     */
    protected $responsive = true;

    /**
     * @var int|array
     */
    protected $gutter = 8;

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
        if ($this->memberKeyAuto) {
            $this->memberKeyAuto = false;
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
     * @param bool $memberKeyAuto
     *
     * @return $this
     */
    public function setMemberKeyAuto(bool $memberKeyAuto = true)
    {
        $this->memberKeyAuto = $memberKeyAuto;

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
     * @return bool
     */
    public function isResponsive(): bool
    {
        return $this->responsive;
    }

    /**
     * @param bool $responsive
     *
     * @return $this
     */
    public function setResponsive(bool $responsive = false)
    {
        $this->responsive = $responsive;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getGutter()
    {
        if (is_int($this->gutter)) {
            return $this->gutter;
        }

        return Helper::jsonStringify($this->gutter);
    }

    /**
     * @param array|int $gutter
     *
     * @return $this
     */
    public function setGutter($gutter)
    {
        $this->gutter = $gutter;

        return $this;
    }
}