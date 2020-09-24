<?php

namespace Leon\BswBundle\Module\Bsw\Modal;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $title = 'Modal';

    /**
     * @var bool
     */
    public $closable = true;

    /**
     * @var string
     */
    public $animate = Abs::MODAL_BSW_ZOOM;

    /**
     * @var int
     */
    public $zIndex = 1000;

    /**
     * @var bool
     */
    public $centered = true;

    /**
     * @var string|int
     */
    public $width = '50%';

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
     * @var string
     */
    public $okText = 'Sure';

    /**
     * @var string
     */
    public $okType = Abs::THEME_PRIMARY;

    /**
     * @var string
     */
    public $cancelText = 'Cancel';

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