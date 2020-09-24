<?php

namespace Leon\BswBundle\Module\Bsw\Result;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $title = 'Operation success';

    /**
     * @var string
     */
    public $subTitle;

    /**
     * @var bool
     */
    public $closable = false;

    /**
     * @var string
     */
    public $animate = Abs::MODAL_BSW_ZOOM;

    /**
     * @var int
     */
    public $zIndex = 1000;

    /**
     * @var string|int
     */
    public $width = Abs::MEDIA_XS;

    /**
     * @var string
     */
    public $wrapClsName = null;

    /**
     * @var bool
     */
    public $keyboard = false;

    /**
     * @var bool
     */
    public $mask = true;

    /**
     * @var bool
     */
    public $maskClosable = false;

    /**
     * @var string
     */
    public $maskAnimate = Abs::MODAL_FADE;

    /**
     * @var bool
     */
    public $centered = true;

    /**
     * @var string
     */
    public $status = Abs::RESULT_STATUS_SUCCESS;

    /**
     * @var string
     */
    public $okText = 'I got it';

    /**
     * @var bool
     */
    public $okShow = true;

    /**
     * @var string
     */
    public $okType = Abs::THEME_PRIMARY;

    /**
     * @var string
     */
    public $cancelText = 'Cancel';

    /**
     * @var bool
     */
    public $cancelShow = false;

    /**
     * @var array
     */
    public $bodyStyle = [];

    /**
     * @var array
     */
    public $maskStyle = [];

    /**
     * @var array
     */
    public $dialogStyle = [];
}