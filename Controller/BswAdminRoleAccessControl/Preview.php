<?php

namespace Leon\BswBundle\Controller\BswAdminRoleAccessControl;

use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminRoleAccessControl::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_role_access_control_persistence', $this->cnf->icon_newly),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Edit record', 'app_bsw_admin_role_access_control_persistence'))->setArgs(
                ['id' => $args->item['id']]
            ),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-role-access-control/preview", name="app_bsw_admin_role_access_control_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}