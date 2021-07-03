<?php

namespace Leon\BswBundle\Controller\BswAdminLogin;

use Leon\BswBundle\Entity\BswAdminLogin;
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
        return BswAdminLogin::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_login_persistence', $this->cnf->icon_newly),
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
            (new Button('Edit record', 'app_bsw_admin_login_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return string
     */
    public function previewCharmLocation(Arguments $args)
    {
        return str_replace('|', ' > ', trim($args->value, '|'));
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-login/preview", name="app_bsw_admin_login_preview")
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