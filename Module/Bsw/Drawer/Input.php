<?php

namespace Leon\BswBundle\Module\Bsw\Drawer;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $title = 'Drawer';

    /**
     * @var bool
     */
    public $closable = true;

    /**
     * @var int
     */
    public $zIndex = 1000;

    /**
     * @var string|int
     */
    public $width = Abs::MEDIA_MD;

    /**
     * @var int
     */
    public $height = 512;

    /**
     * @var string
     */
    public $placement = Abs::POS_LEFT;

    /**
     * @var string
     */
    public $placementInIframe = Abs::POS_LEFT;

    /**
     * @var string
     */
    public $placementInMobile = Abs::POS_BOTTOM;

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
    public $maskClosable = true;

    /**
     * @var string
     */
    public $okText = 'Sure';

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
    public $maskStyle = [];

    /**
     * @var array
     */
    public $wrapStyle = [];

    /**
     * @var array
     */
    public $drawerStyle = [];

    /**
     * @var array
     */
    public $headerStyle = [];

    /**
     * @var array
     */
    public $bodyStyle = [];

    /**
     * @var string
     */
    public $clsName = 'bsw-align-right';
}