<?php

namespace Leon\BswBundle\Controller\BswWorkTeam;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Bsw work team
 */
class Acme extends BswBackendController
{
    use Preview;
    use Persistence;
}