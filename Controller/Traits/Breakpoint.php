<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;

trait Breakpoint
{
    /**
     * @var array
     */
    protected $breakpoint = [];

    /**
     * @var int
     */
    protected $breakpointExit;

    /**
     * Breakpoint
     *
     * @param int   $breakpoint
     * @param array $args
     */
    public function breakpointAppend(int $breakpoint, ...$args)
    {
        $this->breakpoint[$breakpoint] = $args;
        $this->breakpointExit = Helper::arrayLastItem($this->breakpoint)[0];
    }

    /**
     * Breakpoint debug
     *
     * @param int   $breakpoint
     * @param mixed $args
     *
     * @return void
     */
    public function breakpointDebug(int $breakpoint, ...$args)
    {
        if (!$this->breakpoint) {
            return null;
        }

        if (!isset($this->breakpoint[$breakpoint])) {
            return null;
        }

        dump(...$args);

        if ($this->breakpointExit === $breakpoint) {
            exit(ErrorDebugExit::CODE);
        }
    }
}