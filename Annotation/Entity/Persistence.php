<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;

/**
 * @Annotation
 */
class Persistence extends Annotation
{
    /**
     * @var mixed
     */
    public $valueShadow;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $typeArgs = [];

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
     * @var string|array
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
     * @var bool
     */
    public $disabled = false;

    /**
     * @var bool
     */
    public $disabledOverall = true;

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
     * @var array
     */
    public $formRules;

    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var int
     */
    public $column;

    /**
     * @var string
     */
    public $tips;

    /**
     * @var string
     */
    public $title;

    /**
     * @var bool
     */
    public $html = false;

    /**
     * @var string
     */
    public $validatorType;

    /**
     * @var bool
     */
    public $ignoreBlank = false;

    /**
     * @var bool
     */
    public $ignore = false;
}
