<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Form\Entity\Button;

class Output extends ArgsOutput
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var array
     */
    public $record = [];

    /**
     * @var Button[]
     */
    public $operates = [];

    /**
     * @var string
     */
    public $formatJson;

    /**
     * @var array
     */
    public $style = [];

    /**
     * @var string
     */
    public $styleJson;

    /**
     * @var array
     */
    public $operateStyle = [];

    /**
     * @var string
     */
    public $operateStyleJson;

    /**
     * @var array
     */
    public $fileListKeyCollect = [];

    /**
     * @var string
     */
    public $fileListKeyCollectJson;

    /**
     * @var array
     */
    public $uploadTipsCollect = [];

    /**
     * @var string
     */
    public $uploadTipsCollectJson;

    /**
     * @var array
     */
    public $fieldHideCollect = [];

    /**
     * @var string
     */
    public $fieldHideCollectJson;

    /**
     * @var array
     */
    public $fieldDisabledCollect = [];

    /**
     * @var string
     */
    public $fieldDisabledCollectJson;

    /**
     * @var array
     */
    public $transferKeysCollect = [];

    /**
     * @var string
     */
    public $transferKeysCollectJson;

    /**
     * @var array
     */
    public $varNameForMetaCollect = [];

    /**
     * @var string
     */
    public $varNameForMetaCollectJson;

    /**
     * @var int
     */
    public $totalColumn;

    /**
     * @var int
     */
    public $labelColumn;
}