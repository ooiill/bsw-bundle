<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;

/**
 * @Annotation
 */
class AccessControl extends Annotation
{
    /**
     * @var bool
     */
    public $join = true; // Join to manage?

    /**
     * @var string
     */
    public $same; // Same to another route

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $title;

    /**
     * @var bool
     */
    public $export = false;
}