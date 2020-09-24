<?php

namespace Leon\BswBundle\Module\Bsw\Drawer;

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
     * @var int
     */
    public $zIndex;

    /**
     * @var string|int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $placement;

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
    public $maskStyleJson;

    /**
     * @var string
     */
    public $wrapStyleJson;

    /**
     * @var string
     */
    public $drawerStyleJson;

    /**
     * @var string
     */
    public $headerStyleJson;

    /**
     * @var string
     */
    public $bodyStyleJson;

    /**
     * @var string
     */
    public $clsName;
}