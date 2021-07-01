<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property AdapterInterface $cache
 */
trait CleanBackend
{
    /**
     * Clean backend cache
     *
     * @Route("/cache/backend", name="app_clean_backend")
     * @Access()
     *
     * @return Response
     * @throws
     */
    public function getCleanBackendAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->cache->clear();

        return $this->responseSuccess('Cache clear success', [], $this->reference());
    }

    /**
     * Clean project cache
     *
     * @Route("/cache/project", name="app_clean_project")
     * @Access()
     *
     * @return Response
     * @throws
     */
    public function getCleanProjectAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->commandCaller('cache:clear');

        return $this->responseSuccess('Cache clear success', [], $this->reference());
    }
}