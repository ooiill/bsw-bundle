<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Module\Scene\Message;

class ArgsOutput
{
    /**
     * @var Message
     */
    public $message;

    /**
     * ArgsOutput constructor.
     *
     * @param ArgsInput $input
     */
    public function __construct(ArgsInput $input = null)
    {
        if (!$input) {
            return;
        }

        foreach ($this as $attribute => $value) {
            if (!property_exists($input, $attribute)) {
                continue;
            }
            if (!empty($value) || $value === 0) {
                continue;
            }
            $this->{$attribute} = $input->{$attribute};
        }
    }
}