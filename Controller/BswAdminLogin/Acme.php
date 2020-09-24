<?php

namespace Leon\BswBundle\Controller\BswAdminLogin;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Bsw admin login
 */
class Acme extends BswBackendController
{
    use Common;
    use Preview;
    use Persistence;
}