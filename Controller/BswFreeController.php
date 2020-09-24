<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Leon\BswBundle\Controller\Traits as CT;

class BswFreeController extends AbstractController
{
    use CT\Foundation;

    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_WEB;
}