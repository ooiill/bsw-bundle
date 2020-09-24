<?php

namespace Leon\BswBundle\Annotation\Entity;

use Leon\BswBundle\Annotation\Annotation;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;

/**
 * @Annotation
 */
class Input extends Annotation
{
    /**
     * @var string
     */
    public $field;

    /**
     * @var bool|string
     */
    public $validator = false;

    /**
     * @var string|array
     */
    public $rules = 'notBlank';

    /**
     * @var array
     */
    public $rulesArgsHandler = [];

    /**
     * @var bool|string
     */
    public $sign = true;

    /**
     * @var string
     */
    public $error = ErrorParameter::class;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $remark;

    /**
     * @var bool Need trans for label?
     */
    public $trans;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string Http method
     */
    public $method;

    /**
     * @var bool Allow html code
     */
    public $html = false;
}