<?php

namespace Leon\BswBundle\Module\Bsw\Operate;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;

class Input extends ArgsInput
{
    /**
     * @var string
     */
    public $size = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $sizeInIframe = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $sizeInMobile = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $position = Abs::POS_TOP;

    /**
     * @var string
     */
    public $clsName = 'bsw-align-right';

    /**
     * @var string
     */
    public $clsNameInIframe = 'bsw-align-left';

    /**
     * @var string
     */
    public $clsNameInMobile = 'bsw-align-right';
}