<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Component\HttpFoundation\Response;

class BswFrontendController extends BswWebController
{
    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_FRONTEND;

    /**
     * @var bool
     */
    protected $webSrc = true;

    /**
     * @var string
     */
    protected $skUser = 'frontend-user-sk';

    /**
     * @var array
     */
    protected $currentSrcCss = [
        'web' => Abs::CSS_WEB,
    ];

    /**
     * @var array
     */
    protected $currentSrcJs = [
        'web' => Abs::JS_WEB,
    ];

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        if ($this->webSrc) {
            $lang = $this->langLatest($this->langMap, 'en');
            $this->appendSrcJsWithKey('lang', Abs::JS_LANG[$lang], Abs::POS_TOP, 'web', true);
            $this->appendSrcJsWithKey('moment-lang', Abs::JS_MOMENT_LANG[$lang], Abs::POS_TOP, 'web', true);
        }
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error|Response
     */
    protected function webShouldAuth(array $args)
    {
        return [];
    }

    /**
     * Access builder
     *
     * @param object $usr
     *
     * @return array
     */
    protected function accessBuilder($usr): array
    {
        return [];
    }
}