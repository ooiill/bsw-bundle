<?php

namespace Leon\BswBundle\Controller\BswAdminRole;

use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminRoleAccessControlRepository;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait Preview
{
    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'roleAccessTotal' => [
                'width'  => 150,
                'align'  => Abs::POS_CENTER,
                'sort'   => 2.1,
                'render' => Abs::HTML_CODE,
            ],
        ];
    }

    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminRole::class;
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_admin_role_persistence', $this->cnf->icon_newly),
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
            (new Button('Edit record'))
                ->setRoute('app_bsw_admin_role_persistence')
                ->setArgs(['id' => $args->item['id']]),

            (new Button('Grant authorization for role'))
                ->setRoute('app_bsw_admin_role_access_control_grant')
                ->setType(Abs::THEME_DANGER)
                ->setArgs(['id' => $args->item['id'], 'target' => $args->item['name']]),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        static $roleAccessTotal;

        /**
         * @var BswAdminRoleAccessControlRepository $roleAccess
         */
        $roleAccess = $this->repo(BswAdminRoleAccessControl::class);

        if (!isset($roleAccessTotal)) {
            $roleAccessTotal = $roleAccess->lister(
                [
                    'alias'  => 'ac',
                    'select' => ['ac.roleId', 'COUNT(ac) AS total'],
                    'where'  => [$this->expr->eq('ac.state', ':state')],
                    'args'   => ['state' => [Abs::NORMAL]],
                    'group'  => ['ac.roleId'],
                ]
            );
            $roleAccessTotal = array_column($roleAccessTotal, 'total', 'roleId');
        }

        $args->hooked['roleAccessTotal'] = $roleAccessTotal[$args->hooked['id']] ?? 0;

        return $args->hooked;
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-role/preview", name="app_bsw_admin_role_preview")
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