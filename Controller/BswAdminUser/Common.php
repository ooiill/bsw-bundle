<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Leon\BswBundle\Entity\BswAdminRole;
use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswAdminRoleRepository;
use Leon\BswBundle\Repository\BswWorkTeamRepository;

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
        $role = [0 => Abs::UNALLOCATED] + $role;

        return $role;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function acmeEnumExtraTeamId(Arguments $args): array
    {
        /**
         * @var BswWorkTeamRepository $teamRepo
         */
        $teamRepo = $this->repo(BswWorkTeam::class);

        $team = $teamRepo->kvp(['name']);
        $team = [0 => Abs::UNALLOCATED] + $team;

        return $team;
    }
}