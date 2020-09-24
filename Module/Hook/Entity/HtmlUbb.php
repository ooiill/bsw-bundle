<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Hook\Hook;
use Leon\BswBundle\Component\Ubb;

class HtmlUbb extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        return (new Ubb)->ubbToHtml($value ?? '');
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        return (new Ubb)->htmlToUbb($value ?? '');
    }
}