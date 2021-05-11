<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Validator\Entity\Password;

class Input extends ArgsInput
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var bool
     */
    public $customHandler = false;

    /**
     * @var array
     */
    public $submit = [];

    /**
     * @var string
     */
    public $fill = 'fill';

    /**
     * @var string
     */
    public $view = 'view';

    /**
     * @var array
     */
    public $style = [];

    /**
     * @var array
     */
    public $operateStyle = [];

    /**
     * @var string
     */
    public $i18nNewly = 'Newly record done';

    /**
     * @var string
     */
    public $i18nModify = 'Modify record done';

    /**
     * @var array
     */
    public $i18nArgs = [];

    /**
     * @var string
     */
    public $nextRoute = '';

    /**
     * @var array
     */
    public $sets = [];

    /**
     * @var string
     */
    public $size = Abs::SIZE_LARGE;

    /**
     * @var string
     */
    public $sizeInIframe = Abs::SIZE_LARGE;

    /**
     * @var string
     */
    public $sizeInMobile = Abs::SIZE_LARGE;

    /**
     * @var string
     */
    public $passwordValidator = Password::class;

    /**
     * @var bool
     */
    public $operatesBlock = false;

    /**
     * @var bool
     */
    public $operatesBlockInIframe = true;

    /**
     * @var bool
     */
    public $operatesBlockInMobile = true;

    /**
     * @var int
     */
    public $totalColumn = Abs::PERSISTENCE_TOTAL_COLUMN;

    /**
     * @var int
     */
    public $labelColumn = Abs::PERSISTENCE_LABEL_COLUMN;
}