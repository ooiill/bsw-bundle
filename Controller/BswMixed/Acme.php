<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Bsw mixed
 */
class Acme extends BswBackendController
{
    use CleanBackend;
    use Export;
    use Language;
    use NumberCaptcha;
    use SiteIndex;
    use Telegram;
    use Theme;
    use ThirdMessage;
}