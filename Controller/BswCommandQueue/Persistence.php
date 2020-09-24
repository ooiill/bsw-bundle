<?php

namespace Leon\BswBundle\Controller\BswCommandQueue;

use Leon\BswBundle\Entity\BswCommandQueue;
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
        return BswCommandQueue::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-command-queue/persistence/{id}", name="app_bsw_command_queue_persistence", requirements={"id": "\d+"})
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