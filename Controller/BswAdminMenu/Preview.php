<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr                $expr
 * @property TranslatorInterface $translator
 */
trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAdminMenu::class;
    }

    /**
     * @return array
     */
    public function previewAnnotation(): array
    {
        return [
            'id'    => ['fixed' => false],
            'value' => [
                'sort'   => 0.1,
                'render' => null,
                'align'  => null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewQuery()
    {
        return [
            'sort' => ['bam.sort' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return array
     */
    public function previewQueryParent()
    {
        return [
            'where' => [$this->expr->eq('bam.menuId', ':menuId')],
            'args'  => ['menuId' => [0]],
            'sort'  => ['bam.sort' => Abs::SORT_ASC],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewQueryChildren(Arguments $args)
    {
        return [
            'where' => [$this->expr->eq('bam.menuId', ':menuId')],
            'args'  => ['menuId' => [$args->parent]],
            'sort'  => ['bam.sort' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Encase', null, $this->cnf->icon_two_box))
                ->setType(Abs::THEME_DANGER)
                ->setSelector(Abs::SELECTOR_CHECKBOX)
                ->setRoute('app_bsw_admin_menu_multiple_encase')
                ->setClick('multipleAction')
                ->setConfirm($this->messageLang('Are you sure'))
                ->setArgs(['refresh' => true]),

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

            new Button('New record', 'app_bsw_admin_menu_persistence', $this->cnf->icon_newly),
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
            (new Button('Sort'))
                ->setType(Abs::THEME_DEFAULT)
                ->setRoute('app_bsw_admin_menu_sort')
                ->setClick('showIFrame')
                ->setName('admin_menu_sort')
                ->setArgs(
                    [
                        'id'     => $args->item['id'],
                        'width'  => Abs::MEDIA_XS,
                        'height' => 222,
                        'title'  => false,
                    ]
                ),
            (new Button('Edit record', 'app_bsw_admin_menu_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-menu/preview", name="app_bsw_admin_menu_preview")
     * @Access(export=true)
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->appendSrcCssWithKey('highlight', Abs::CSS_HIGHLIGHT_GH);
        $this->appendSrcJsWithKey('highlight', Abs::JS_HIGHLIGHT);

        return $this->showPreview(['childrenRelationField' => true]);
    }
}