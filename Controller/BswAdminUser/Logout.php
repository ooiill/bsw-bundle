<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property Session $session
 */
trait Logout
{
    /**
     * User logout
     *
     * @Route("/bsw-admin-user/logout", name="app_bsw_admin_user_logout")
     *
     * @return Response
     */
    public function getLogoutAction(): Response
    {
        $this->session->clear();

        return $this->redirectToRoute($this->cnf->route_login);
    }
}