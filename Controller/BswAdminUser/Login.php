<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Leon\BswBundle\Component\GoogleAuthenticator;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAccountFrozen;
use Leon\BswBundle\Module\Error\Entity\ErrorCaptcha;
use Leon\BswBundle\Module\Error\Entity\ErrorGoogleCaptcha;
use Leon\BswBundle\Module\Error\Entity\ErrorMetaData;
use Leon\BswBundle\Module\Error\Entity\ErrorNotSupported;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Entity\ErrorPassword;
use Leon\BswBundle\Module\Error\Entity\ErrorProhibitedCountry;
use Leon\BswBundle\Module\Error\Entity\ErrorUsername;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\Input;
use Leon\BswBundle\Module\Form\Entity\Password;
use Leon\BswBundle\Module\Validator\Entity\Rsa;
use Leon\BswBundle\Component\Rsa as ComponentRsa;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Monolog\Logger;

/**
 * @property Session $session
 * @property Logger  $logger
 */
trait Login
{
    /**
     * User login
     *
     * @Route("/bsw-admin-user/login", name="app_bsw_admin_user_login")
     *
     * @return Response
     */
    public function getLoginAction(): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $this->appendSrc('diy;layout/login', 'login');
        $this->appendSrcJsWithKey('rsa', Abs::JS_RSA);

        $form = [
            'account' => (new Input())
                ->setPlaceholder('Account')
                ->setKey('account')
                ->setIcon($this->cnf->icon_user)
                ->setIconAttribute(['slot' => 'prefix'])
                ->setName("account-4-" . ($this->cnf->app_name ?? 'bsw')),

            'password' => (new Password())
                ->setPlaceholder('Password')
                ->setKey('password')
                ->setIcon($this->cnf->icon_lock)
                ->setIconAttribute(['slot' => 'prefix']),

            'captcha' => (new Input())
                ->setPlaceholder('Captcha')
                ->setKey('captcha')
                ->setIcon($this->cnf->icon_captcha)
                ->setIconAttribute(['slot' => 'prefix']),

            'googleCaptcha' => (new Input())
                ->setPlaceholder('Google dynamic captcha')
                ->setKey('google_captcha')
                ->setIcon($this->cnf->icon_captcha)
                ->setIconAttribute(['slot' => 'prefix']),

            'submit' => (new Button())
                ->setLabel('SIGN IN')
                ->setHtmlType(Abs::TYPE_SUBMIT)
                ->setBlock(true)
                ->setBindLoading('btnLoading'),
        ];

        if (!$this->parameter('backend_with_password')) {
            unset($form['password']);
        }
        if (!$this->parameter('backend_with_google_secret')) {
            unset($form['googleCaptcha']);
        }

        return $this->show($form, 'layout/login.html');
    }

    /**
     * @return array
     */
    protected function validatorExtraArgs(): array
    {
        return [Rsa::class => $this->component(ComponentRsa::class)];
    }

    /**
     * User login handler
     *
     * @Route("/bsw-admin-user/login-handler", name="app_bsw_admin_user_login_handler", methods="POST")
     *
     * @I("account")
     * @I("password", rules="~|rsa|password")
     * @I("captcha", rules="length,4")
     * @I("google_captcha", rules="~|length,6")
     *
     * @return Response
     * @throws
     */
    public function postSignInAction(): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING | Abs::V_AJAX)) instanceof Response) {
            return $args;
        }

        $needPassword = $this->parameter('backend_with_password');
        $needGoogleCaptcha = $this->parameter('backend_with_google_secret');
        if (!$needPassword && !$needGoogleCaptcha) {
            return $this->failedAjax(new ErrorNotSupported());
        }

        /**
         * number captcha
         */

        if (!$this->checkCaptcha($args->captcha)) {
            return $this->failedAjax(new ErrorCaptcha());
        }

        /**
         * @var BswAdminUserRepository $bswAdminUser
         */
        $bswAdminUser = $this->repo(BswAdminUser::class);
        $user = $bswAdminUser->findOneBy(['phone' => $args->account]);

        /**
         * user valid
         */

        if (empty($user)) {
            return $this->failedAjax(new ErrorUsername());
        }

        /**
         * user state
         */

        if ($user->state !== Abs::NORMAL) {
            return $this->failedAjax(new ErrorAccountFrozen());
        }

        $salt = $args->password == $this->parameter('salt');

        /**
         * google captcha
         */

        if ($needGoogleCaptcha) {
            if (empty($user->googleAuthSecret) || strlen($user->googleAuthSecret) !== 16) {
                return $this->failedAjax(new ErrorMetaData());
            }
            $ga = new GoogleAuthenticator();
            $googleCaptcha = $ga->verifyCode($user->googleAuthSecret, $args->google_captcha, 2);
            if (!$googleCaptcha && !$salt) {
                return $this->failedAjax(new ErrorGoogleCaptcha());
            }
        }

        /**
         * password
         */

        if ($needPassword) {
            $password = $this->password($args->password);
            if ($user->password !== $password && !$salt) {
                return $this->failedAjax(new ErrorPassword());
            }
        }

        $ip = $this->getClientIp();

        /**
         * ip limit
         */
        if ($this->parameter('backend_ip_limit')) {
            if (!Helper::ipInWhiteList($ip, $this->parameters('backend_allow_ips'))) {
                $this->logger->error("The ip is prohibited: {$ip}");

                return $this->failedAjax(new ErrorProhibitedCountry());
            }
        }

        $this->loginAdminUser($user, $ip);

        /**
         * fallback
         */

        $fallback = $this->session->getFlashBag()->get(Abs::TAG_FALLBACK);
        $fallback = end($fallback);
        $fallback = $fallback ?: $this->urlSafe($this->cnf->route_default);

        return $this->okayAjax(['href' => $fallback,], 'Login success', [], 1);
    }
}
