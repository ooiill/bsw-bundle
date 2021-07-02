<?php

namespace Leon\BswBundle\Module\Bsw\Preview;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Scene\Choice;

class Input extends ArgsInput
{
    /**
     * @var Choice
     */
    public $choice;

    /**
     * @var bool
     */
    public $choiceFixed = true;

    /**
     * @var bool
     */
    public $choiceFixedInIframe = true;

    /**
     * @var bool
     */
    public $choiceFixedInMobile = false;

    /**
     * @var bool
     */
    public $border = true;

    /**
     * @var bool
     */
    public $borderInIframe = true;

    /**
     * @var bool
     */
    public $borderInMobile = true;

    /**
     * @var string
     */
    public $childrenName = Abs::TAG_CHILDREN;

    /**
     * @var bool
     */
    public $expandRows = false;

    /**
     * @var bool
     */
    public $expandRowByClick = false;

    /**
     * @var int
     */
    public $expandIconColumnIndex;

    /**
     * @var int
     */
    public $indentSize = 20;

    /**
     * @var array
     */
    public $scroll = [];

    /**
     * @var bool
     */
    public $removeOperate = false;

    /**
     * @var bool
     */
    public $removeOperateInIframe = true;

    /**
     * @var bool
     */
    public $removeOperateInMobile = false;

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
     * @var array
     */
    public $pageSizeOptions = Abs::PG_PAGE_SIZE_OPTIONS;

    /**
     * @var bool
     */
    public $paginationShow = true;

    /**
     * @var string
     */
    public $paginationClsName = 'bsw-align-center';

    /**
     * @var bool
     */
    public $paginationAnyCase = true;

    /**
     * @var bool
     */
    public $paginationSizeChanger = true;

    /**
     * @var string
     */
    public $paginationSize = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $paginationSizeInIframe = Abs::SIZE_DEFAULT;

    /**
     * @var string
     */
    public $paginationSizeInMobile = Abs::SIZE_SMALL;

    /**
     * @var bool
     */
    public $paginationSimple = false;

    /**
     * @var bool
     */
    public $paginationSimpleInIframe = false;

    /**
     * @var bool
     */
    public $paginationSimpleInMobile = true;

    /**
     * @var int
     */
    public $dynamic = 0;

    /**
     * @var string
     */
    public $rowClsNameMethod = 'previewRowClsName';

    /**
     * @var string
     */
    public $recordOperatesSize = Abs::SIZE_SMALL;

    /**
     * @var bool
     */
    public $header = false;

    /**
     * @var bool
     */
    public $footer = false;

    /**
     * @var bool|string
     */
    public $childrenRelationField = false;

    /**
     * @var string
     */
    public $childrenRowClsName = 'bsw-row-children';

    /**
     * @var bool
     */
    public $scrollXOperate = true;

    /**
     * @var bool
     */
    public $scrollXOperateInIframe = true;

    /**
     * @var bool
     */
    public $scrollXOperateInMobile = false;

    /**
     * @var int
     */
    public $scrollXStepPx = 500;

    /**
     * @var int
     */
    public $scrollXBottomPx = 44;

    /**
     * @var bool
     */
    public $actionBtnForceLink = false;

    /**
     * @var bool
     */
    public $actionBtnForceNoIcon = false;

    /**
     * @var ?string
     */
    public $actionBtnSplit = null;

    /**
     * @var int|float
     */
    public $actionColBorder = 16;

    /**
     * @var int|float
     */
    public $actionBtnIconWidth = 15.4;

    /**
     * @var int|float
     */
    public $actionBtnBorder = 10;

    /**
     * @var int|float
     */
    public $actionBtnGap = 8;

    /**
     * @var array
     */
    public $actionByteMapPx = [
        1 => 6,
        3 => 12,
    ];

    /**
     * @var int
     */
    public $actionMinWidth = 64;

    /**
     * @var int
     */
    public $actionMaxWidth = 512;
}