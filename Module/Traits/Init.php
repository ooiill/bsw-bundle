<?php

namespace Leon\BswBundle\Module\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait Init
{
    /**
     * @var string
     */
    protected $beforeInit = Abs::FN_BEFORE_BOOTSTRAP;

    /**
     * @var string
     */
    protected $init = Abs::FN_BOOTSTRAP;

    /**
     * Before init
     */
    protected function beforeInit()
    {
        if (method_exists($this, $this->beforeInit)) {
            $this->{$this->beforeInit}();
        }
    }

    /**
     * Init
     */
    protected function init()
    {
        if (method_exists($this, $this->init)) {
            $this->{$this->init}();
        }
    }
}