<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;
use Leon\BswBundle\Module\Entity\Abs;

class Week extends Datetime
{
    /**
     * Date constructor.
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * @see https://momentjs.com/docs/#/displaying/format
         */
        $this->setFormat('YYYY-WW');
        $this->setShowTime(false);
    }

    /**
     * @return string
     */
    public function getItemName(): string
    {
        return 'week-picker';
    }
}