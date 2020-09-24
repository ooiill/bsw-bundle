<?php

namespace Leon\BswBundle\Module\Bsw\Modal;

use Leon\BswBundle\Module\Bsw\ArgsOutput;

class Output extends ArgsOutput
{
    /**
     * @var string
     */
    public $title;

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
     * @var bool
     */
    public $centered;

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
     * @var string
     */
    public $okText;

    /**
     * @var string
     */
    public $okType;

    /**
     * @var string
     */
    public $cancelText;

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