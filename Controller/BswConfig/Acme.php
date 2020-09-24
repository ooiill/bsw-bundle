<?php

namespace Leon\BswBundle\Controller\BswConfig;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Bsw config
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
}