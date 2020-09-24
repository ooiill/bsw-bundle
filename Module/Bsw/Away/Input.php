<?php

namespace Leon\BswBundle\Module\Bsw\Away;

use Leon\BswBundle\Module\Bsw\ArgsInput;

class Input extends ArgsInput
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    public $relation = [];

    /**
     * @var string
     */
    public $i18nAway = 'Away record done';

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
}