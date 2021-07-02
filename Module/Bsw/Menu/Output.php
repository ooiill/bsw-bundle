<?php

namespace Leon\BswBundle\Module\Bsw\Menu;

use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Scene\Menu;

class Output extends ArgsOutput
{
    /**
     * @var Menu[]
     */
    public $masterMenu = [];

    /**
     * @var Menu[]
     */
    public $masterMenuForRender = [];

    /**
     * @var Menu[][]
     */
    public $slaveMenu = [];

    /**
     * @var Menu[][]
     */
    public $slaveMenuForRender = [];

    /**
     * @var array
     */
    public $masterMenuDetail = [];

    /**
     * @var array
     */
    public $masterMenuDetailForRender = [];

    /**
     * @var array
     */
    public $slaveMenuDetail = [];

    /**
     * @var array
     */
    public $slaveMenuDetailForRender = [];

    /**
     * @var int
     */
    public $parent = 0;

    /**
     * @var int
     */
    public $current = 0;
}