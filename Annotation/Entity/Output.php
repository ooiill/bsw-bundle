<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @Annotation
 */
class Output extends Annotation
{
    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $extra;

    /**
     * @var string
     */
    public $position = Abs::POS_BOTTOM; // Effect just when extra not null

    /**
     * @var string
     */
    public $prefix;

    /**
     * @var int
     */
    public $tab = 0;

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
    public $type = Abs::T_STRING;

    /**
     * @var array
     */
    public $enum;
}