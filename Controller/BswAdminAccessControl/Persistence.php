<?php

namespace Leon\BswBundle\Controller\BswAdminAccessControl;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAdminAccessControl::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-admin-access-control/persistence/{id}", name="app_bsw_admin_access_control_persistence", requirements={"id": "\d+"})
     * @Access(class="danger", title="Dangerous permission, please be careful")
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