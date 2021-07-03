<?php

namespace Leon\BswBundle\Controller\BswWorkTaskTrail;

use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Leon\BswBundle\Repository\BswWorkTaskRepository;

/**
 * @property Expr $expr
 */
trait Common
{
    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraUserId(Arguments $args): array
    {
        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);

        return $adminRepo->kvp(['name']);
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraTaskId(Arguments $args): array
    {
        $filter = [];
        if ($args->scene === Abs::TAG_PERSISTENCE && !$args->id) {
            $filter = [
                'where' => [$this->expr->gt('bwt.state', ':state')],
                'args'  => ['state' => [0]],
            ];
        }

        /**
         * @var BswWorkTaskRepository $taskRepo
         */
        $taskRepo = $this->repo(BswWorkTask::class);

        $list = $taskRepo->filters($filter)->lister(
            [
                'limit'  => 0,
                'select' => ['bwt.id', 'bwt.title'],
            ]
        );

        return array_column($list, 'title', 'id');
    }
}