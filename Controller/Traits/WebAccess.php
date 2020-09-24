<?php

namespace Leon\BswBundle\Controller\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property TranslatorInterface $translator
 */
trait WebAccess
{
    /**
     * @var array
     */
    protected $access = [];

    /**
     * Master manager
     *
     * @param object $usr
     *
     * @return bool
     */
    protected function root($usr): bool
    {
        return in_array($usr->{$this->cnf->usr_uid}, $this->parameter('backend_auth_root_ids'));
    }

    /**
     * Access builder
     *
     * @param object $usr
     *
     * @return array
     */
    abstract protected function accessBuilder($usr): array;

    /**
     * Routes is access
     *
     * @param array $routes
     * @param int   $passNeed
     *
     * @return mixed
     */
    public function routeIsAccess(array $routes, ?int $passNeed = null)
    {
        if (empty($routes)) {
            return true;
        }

        $passNow = 0;
        $passNeed = $passNeed ?? count($routes);

        foreach ($routes as $route) {
            if (empty($route)) {
                $passNow += 1;
            } else {
                $access = $this->access[$route] ?? false;
                $passNow += ($access === true ? 1 : 0);
            }
        }

        return $passNow >= $passNeed;
    }
}