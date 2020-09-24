<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDbPersistence;
use Leon\BswBundle\Repository\BswAdminMenuRepository;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAdminMenu::class;
    }

    /**
     * @return array
     */
    public function persistenceAnnotation(): array
    {
        $iconA = $this->getButtonHtml(
            (new Button('a:class'))
                ->setType(Abs::THEME_DEFAULT)
                ->setSize(Abs::SIZE_SMALL)
                ->setArgsUseMeta(true)
                ->setArgs(
                    [
                        'location' => $this->cnf->ant_icon_url,
                        'window'   => true,
                    ]
                ),
            true
        );

        $iconB = $this->getButtonHtml(
            (new Button('b:symbol'))
                ->setType(Abs::THEME_DEFAULT)
                ->setSize(Abs::SIZE_SMALL)
                ->setArgsUseMeta(true)
                ->setArgs(
                    [
                        'location' => $this->cnf->font_symbol_url,
                        'window'   => true,
                    ]
                ),
            true
        );

        return [
            'icon' => [
                'title' => "{$iconA} / {$iconB}",
            ],
        ];
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-admin-menu/persistence/{id}", name="app_bsw_admin_menu_persistence", requirements={"id": "\d+"})
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

    /**
     * @return string
     */
    public function sortEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function sortAnnotationOnly(): array
    {
        return ['id' => true, 'sort' => true];
    }

    /**
     * Sort record
     *
     * @Route("/bsw-admin-menu/sort/{id}", name="app_bsw_admin_menu_sort", requirements={"id": "\d+"})
     * @Access(same="app_bsw_admin_menu_persistence")
     *
     * @param int $id
     *
     * @return Response
     */
    public function sort(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(
            [
                'id'   => $id,
                'sets' => ['function' => 'refreshPreview',],
            ]
        );
    }

    /**
     * Multiple encase
     *
     * @Route("/bsw-admin-menu/multiple-encase", name="app_bsw_admin_menu_multiple_encase")
     * @Access()
     *
     * @I("ids", rules="arr")
     *
     * @return Response
     */
    public function multipleEncase(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $ids = array_map('intval', array_column($args->ids, 'id'));

        /**
         * @var BswAdminMenuRepository $menu
         */
        $menu = $this->repo(BswAdminMenu::class);
        $effect = $menu->updater(
            [
                'where' => [$this->expr->in('bam.id', $ids)],
                'set'   => ["bam.state" => ':state'],
                'args'  => ['state' => [Abs::CLOSE]],
            ]
        );

        if ($effect === false) {
            return $this->responseError(new ErrorDbPersistence(), $menu->pop());
        }

        return $this->responseSuccess(
            'Multiple action success, total {{ num }}',
            ['{{ num }}' => $effect]
        );
    }
}