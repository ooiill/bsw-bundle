<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @Annotation
 */
class Mixed extends Annotation
{
    /**
     * @var string
     */
    public $field;

    /**
     * @var bool
     */
    public $order = false;

    /**
     * @var array
     */
    public $orderDirections = [Abs::SORT_ASC_LONG, Abs::SORT_DESC_LONG];

    /**
     * @var bool
     */
    public $sort = false;

    /**
     * @var array
     */
    public $sortDirections = [Abs::SORT_ASC_LONG, Abs::SORT_DESC_LONG];
}