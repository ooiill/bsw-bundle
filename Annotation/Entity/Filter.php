<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;

/**
 * @Annotation
 */
class Filter extends Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $field;

    /**
     * @var bool
     */
    public $adopt = false;

    /**
     * @var mixed
     */
    public $index;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $typeArgs = [];

    /**
     * @var string
     */
    public $group;

    /**
     * @var string
     */
    public $filter;

    /**
     * @var array
     */
    public $filterArgs = [];

    /**
     * @var number
     */
    public $sort = 99;

    /**
     * @var bool
     */
    public $show = true; // No element

    /**
     * @var bool
     */
    public $hide; // Render to element but display none

    /**
     * @var int
     */
    public $showPriority = 0;

    /**
     * @var array|string
     */
    public $hook;

    /**
     * @var string
     */
    public $label;

    /**
     * @var bool Need trans for label?
     */
    public $trans;

    /**
     * @var array|bool
     */
    public $enum;

    /**
     * @var bool|string|array
     */
    public $enumExtra;

    /**
     * @var string|array|callable
     */
    public $enumHandler;

    /**
     * @var array
     */
    public $style = [];

    /**
     * @var string
     */
    public $placeholder;

    /**
     * @var int
     */
    public $column;

    /**
     * @var string
     */
    public $title;
}