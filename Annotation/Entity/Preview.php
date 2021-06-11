<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;

/**
 * @Annotation
 */
class Preview extends Annotation
{
    /**
     * @var number
     */
    public $sort = 99;

    /**
     * @var bool
     */
    public $show = true;

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
     * @var string
     */
    public $align;

    /**
     * @var string
     */
    public $clsName;

    /**
     * @var bool
     */
    public $ellipsis = false;

    /**
     * @var bool
     */
    public $headerColumn = 1;

    /**
     * @var string
     */
    public $customRender;

    /**
     * @var false|string
     */
    public $fixed;

    /**
     * @var int
     */
    public $width;

    /**
     * @var bool (Priority first)
     */
    public $html = false;

    /**
     * @var array|string (Priority second)
     * @license Enum{"pink", "red", "orange", "green", "cyan", "blue", "purple", "#color"}
     */
    public $dress;

    /**
     * @var array
     */
    public $slots = [];

    /**
     * @var string
     */
    public $slotsTips = null;

    /**
     * @var string For vue-slot (Priority latest)
     */
    public $render;

    /**
     * @var bool
     */
    public $status = false;

    /**
     * @var array|bool
     */
    public $enum;

    /**
     * @var array|bool|string
     */
    public $enumExtra;

    /**
     * @var string|array|callable
     */
    public $enumHandler;
}