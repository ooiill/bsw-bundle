<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Entity\BswAdminRoleAccessControl;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminAccessControlRepository;
use Leon\BswBundle\Repository\BswAdminRoleAccessControlRepository;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
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
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminUser::class;
    }

    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'avatarAttachmentImage' => [
                'sort' => 2.01,
            ],
            'roleAccessTotal'       => [
                'width'  => 130,
                'align'  => Abs::POS_CENTER,
                'sort'   => 3.1,
                'render' => Abs::HTML_CODE,
            ],
            'userAccessTotal'       => [
                'width'  => 130,
                'align'  => Abs::POS_CENTER,
                'sort'   => 3.2,
                'render' => Abs::HTML_CODE,
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewTailor(): array
    {
        return [
            Tailor\AttachmentIcon::class => [
                0 => 'avatarAttachmentId',
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Selected', null, $this->cnf->icon_submit_form))
                ->setSelector(Abs::SELECTOR_RADIO)
                ->setClick('fillParentForm')
                ->setScene(Abs::SCENE_IFRAME)
                ->setArgs(
                    [
                        'repair'   => $this->getArgs('repair'),
                        'selector' => 'id',
                    ]
                ),

            new Button('New record', 'app_bsw_admin_user_persistence', $this->cnf->icon_newly),
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
                ->setRoute('app_bsw_admin_user_persistence')
                ->setArgs(['id' => $args->item['id']]),

            (new Button('Google qr code'))
                ->setType(Abs::THEME_DEFAULT)
                ->setRoute('app_bsw_admin_user_google_qr_code')
                ->setClick('showModalAfterRequest')
                ->setArgs(
                    [
                        'width'        => 280,
                        'id'           => $args->item['id'],
                        'closable'     => false,
                        'keyboard'     => true,
                        'maskClosable' => true,
                        'class'        => 'bsw-modal-title-center',
                    ]
                ),

            (new Button('Grant authorization for user'))
                ->setRoute('app_bsw_admin_access_control_grant')
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
         * @var BswAdminAccessControlRepository     $userAccess
         */
        $roleAccess = $this->repo(BswAdminRoleAccessControl::class);
        $userAccess = $this->repo(BswAdminAccessControl::class);

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

        $args->hooked['roleAccessTotal'] = $roleAccessTotal[$args->hooked['roleId']] ?? 0;
        $args->hooked['userAccessTotal'] = $userAccess->count(
            ['userId' => $args->hooked['id'], 'state' => Abs::NORMAL]
        );

        return $args->hooked;
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-user/preview", name="app_bsw_admin_user_preview")
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
