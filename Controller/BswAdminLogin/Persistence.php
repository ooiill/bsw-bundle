<?php

namespace Leon\BswBundle\Controller\BswAdminLogin;

use Leon\BswBundle\Entity\BswAdminLogin;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAdminLogin::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-admin-login/persistence/{id}", name="app_bsw_admin_login_persistence", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function persistence(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }
}