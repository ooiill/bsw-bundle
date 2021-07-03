<?php

namespace Leon\BswBundle\Controller\BswAdminRoleAccessControl;

use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminRoleRepository;

trait Common
{
    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraRoleId(Arguments $args): array
    {
        /**
         * @var BswAdminRoleRepository $roleRepo
         */
        $roleRepo = $this->repo(BswAdminRole::class);

        $role = $roleRepo->kvp(['name']);
        $role = [0 => Abs::NIL] + $role;

        return $role;
    }
}