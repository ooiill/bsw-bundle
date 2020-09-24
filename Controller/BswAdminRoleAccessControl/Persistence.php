<?php

namespace Leon\BswBundle\Controller\BswAdminRoleAccessControl;

use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAdminRoleAccessControl::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-admin-role-access-control/persistence/{id}", name="app_bsw_admin_role_access_control_persistence", requirements={"id": "\d+"})
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