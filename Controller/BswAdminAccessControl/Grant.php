<?php

namespace Leon\BswBundle\Controller\BswAdminAccessControl;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminAccessControl;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Repository\BswAdminAccessControlRepository;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

trait Grant
{
    /**
     * @param int $id
     *
     * @return array
     */
    protected function listForm(int $id): array
    {
        $form = $this->getAccessOfRender();
        $access = $this->getAccessOfUserWithRole($id);

        foreach ($form as &$item) {
            $item['value'] = [];
            foreach ($item['enum'] as $route => $info) {
                if (isset($access[$route])) {
                    array_push($item['value'], $route);
                }
            }
        }

        foreach ($form as &$item) {

            /**
             * @var Checkbox $checkbox
             */
            $checkbox = $item['type'];
            $checkbox->setOptions($item['enum']);
            $checkbox->setValue($item['value']);
        }

        return $form;
    }

    /**
     * @param array $form
     *
     * @return string
     */
    protected function listRender(array $form): string
    {
        $render = [];
        foreach ($form as $key => $item) {

            /**
             * @var Checkbox $checkbox
             */
            $checkbox = $item['type'];
            $render[$key] = array_keys($checkbox->getOptionsArray());
        }

        return Helper::jsonStringify($render);
    }

    /**
     * Get disabled route
     *
     * @param int $userId
     *
     * @return array
     */
    protected function disabled(int $userId = null): array
    {
        // Access from render
        $accessRender = $this->getAccessOfAll();
        $accessDanger = array_filter(Helper::arrayColumn($accessRender, ['class', 'title']));

        // Access from current administrator user id
        $accessCurrentAdmin = $this->getAccessOfUserWithRole($this->usr('usr_uid'));
        $unAccessCurrentAdmin = array_diff(array_keys($accessRender), array_keys($accessCurrentAdmin));
        $unAccessCurrentAdmin = Helper::arrayValuesSetTo($unAccessCurrentAdmin, true, true);

        // Access from other administrators user id
        $accessAlreadyOtherAdmin = $this->getAccessOfRoleByUserId($userId);
        $accessDisabled = array_merge($accessAlreadyOtherAdmin, $unAccessCurrentAdmin);

        // Access from root
        if ($this->root($this->usr)) {
            $accessDisabled = $accessAlreadyOtherAdmin;
        }

        return [$accessDisabled, $accessDanger];
    }

    /**
     * Grant custom handler
     *
     * @param Arguments $args
     *
     * @return Message
     * @throws
     */
    public function grantCustomHandler(Arguments $args)
    {
        $form = $args->submit;

        $id = intval(Helper::dig($form, 'id'));
        $id = $id > 0 ? $id : null;
        $routes = $form ? array_merge(...array_values($form)) : [];

        [$disabled] = $this->disabled($id);
        $routes = array_diff($routes, array_keys($disabled));

        /**
         * @var BswAdminAccessControlRepository $access
         */
        $access = $this->repo(BswAdminAccessControl::class);
        $result = $access->transactional(
            function () use ($access, $id, $routes) {

                $effect = $access->away(['userId' => $id]);
                if ($effect === false) {
                    throw new RepositoryException($access->pop());
                }

                $routesHandling = [];
                foreach ($routes as $route) {
                    $routesHandling[] = [
                        'userId'    => $id,
                        'routeName' => $route,
                    ];
                }

                $effect = $access->newlyMultiple($routesHandling);
                if ($effect === false) {
                    throw new RepositoryException($access->pop());
                }
            }
        );

        if ($result === false) {
            return (new Message('Authorized failed'))
                ->setClassify(Abs::TAG_CLASSIFY_ERROR);
        }

        return (new Message('Authorized success'))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS)
            ->setRoute('app_bsw_admin_user_preview');
    }

    /**
     * Grant authorization for user
     *
     * @Route("/bsw-admin-access-control/grant", name="app_bsw_admin_access_control_grant")
     * @Access(class="danger", title="Dangerous permission, please be careful")
     *
     * @I("id", rules="mysqlInt")
     *
     * @return Response
     */
    public function grant(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->appendSrcJsWithKey('grant', 'diy;layout/grant');
        if ($target = $this->getArgs('target')) {
            $this->changeCrumbs("%s >> {$target}");
        }

        [$disabled, $danger] = $this->disabled($args->id);

        return $this->showPersistence(
            [
                'id'            => $args->id,
                'danger'        => $danger,
                'disabled'      => $disabled,
                'disabledJson'  => Helper::jsonStringify(array_keys($disabled)),
                'customHandler' => true,
                'afterModule'   => [
                    'form'   => function (array $logic) {
                        return $this->listForm($logic['id']);
                    },
                    'render' => function (array $logic) {
                        return $this->listRender($logic['form']);
                    },
                ],
            ],
            [],
            'layout/grant.html'
        );
    }
}