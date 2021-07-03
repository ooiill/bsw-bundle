<?php

namespace Leon\BswBundle\Controller\BswAdminUser;

use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Persistence\Tailor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Profile
{
    /**
     * @return string
     */
    public function profileEntity(): string
    {
        return BswAdminUser::class;
    }

    /**
     * @return array
     */
    public function profileTailor(): array
    {
        return [
            Tailor\NewPassword::class => [
                0 => 'password',
            ],
        ];
    }

    /**
     * @return array
     */
    public function profileAnnotation(): array
    {
        return [
            'phone'      => ['disabled' => true],
            'roleId'     => false,
            'teamId'     => false,
            'teamLeader' => false,
            'telegramId' => false,
            'state'      => false,
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function profileAfterSubmit(Arguments $args)
    {
        $args->submit['id'] = $this->usr('usr_uid');

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * User profile
     *
     * @Route("/bsw-admin-user/profile", name="app_bsw_admin_user_profile")
     *
     * @return Response
     */
    public function profile(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $this->usr('usr_uid')]);
    }
}