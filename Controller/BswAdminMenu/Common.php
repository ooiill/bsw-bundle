<?php

namespace Leon\BswBundle\Controller\BswAdminMenu;

use Leon\BswBundle\Entity\BswAdminMenu;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminMenuRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr                $expr
 * @property TranslatorInterface $translator
 */
trait Common
{
    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraMenuId(Arguments $args): array
    {
        $filter = [
            'where' => [
                $this->expr->eq('kvp.menuId', ':parent'),
                $this->expr->eq('kvp.routeName', ':route'),
            ],
            'args'  => [
                'parent' => [0],
                'route'  => ['', false],
            ],
            'sort'  => ['kvp.sort' => Abs::SORT_ASC],
        ];

        /**
         * @var BswAdminMenuRepository $menuRepo
         */
        $menuRepo = $this->repo(BswAdminMenu::class);

        $menu = $menuRepo->kvp(['value'], Abs::PK, null, $filter);
        $menu = [0 => '(Top Menu)'] + $menu;

        $menu = array_map(
            function ($v) {
                return $this->twigLang($v);
            },
            $menu
        );

        return $menu;
    }
}