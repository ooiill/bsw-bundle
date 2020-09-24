<?php

namespace Leon\BswBundle\Module\Bsw\Filter;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var array
     */
    public $filter = [];

    /**
     * @var array
     */
    public $group = [];

    /**
     * @var array
     */
    public $diffuse = [];

    /**
     * @var array
     */
    public $condition = [];

    /**
     * @var Button[]
     */
    public $operates = [];

    /**
     * @var bool
     */
    public $showLabel;

    /**
     * @var string
     */
    public $formatJson;

    /**
     * @var int
     */
    public $columnPx;

    /**
     * @var int
     */
    public $maxShow;

    /**
     * @var array
     */
    public $showList = [];

    /**
     * @var string
     */
    public $showListJson;

    /**
     * @var array
     */
    public $showFull = [];

    /**
     * @var string
     */
    public $showFullJson;

    /**
     * @var string
     */
    public $textShow;

    /**
     * @var string
     */
    public $textHide;

    /**
     * @var string
     */
    public $size;
}