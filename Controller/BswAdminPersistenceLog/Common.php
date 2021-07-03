<?php

namespace Leon\BswBundle\Controller\BswAdminPersistenceLog;

use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminUserRepository;

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

        $role = $adminRepo->kvp(['name']);
        $role = [0 => Abs::NIL] + $role;

        return $role;
    }
}