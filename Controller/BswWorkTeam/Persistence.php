<?php

namespace Leon\BswBundle\Controller\BswWorkTeam;

use Leon\BswBundle\Entity\BswWorkTeam;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswWorkTeam::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-work-team/persistence/{id}", name="app_bsw_work_team_persistence", requirements={"id": "\d+"})
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
}