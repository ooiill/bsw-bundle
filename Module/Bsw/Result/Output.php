<?php

namespace Leon\BswBundle\Module\Bsw\Result;

use Leon\BswBundle\Module\Bsw\ArgsOutput;

class Output extends ArgsOutput
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $subTitle;

    /**
     * @var bool
     */
    public $closable;

    /**
     * @var string
     */
    public $animate;

    /**
     * @var int
     */
    public $zIndex;

    /**
     * @var string|int
     */
    public $width;

    /**
     * @var string
     */
    public $wrapClsName;

    /**
     * @var bool
     */
    public $keyboard;

    /**
     * @var bool
     */
    public $mask;

    /**
     * @var bool
     */
    public $maskClosable;

    /**
     * @var string
     */
    public $maskAnimate;

    /**
     * @var bool
     */
    public $centered;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $okText;

    /**
     * @var bool
     */
    public $okShow;

    /**
     * @var string
     */
    public $okType;

    /**
     * @var string
     */
    public $cancelText;

    /**
     * @var bool
     */
    public $cancelShow;

    /**
     * @var string
     */
    public $bodyStyleJson;

    /**
     * @var string
     */
    public $maskStyleJson;

    /**
     * @var string
     */
    public $dialogStyleJson;
}