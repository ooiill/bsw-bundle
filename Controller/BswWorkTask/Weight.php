<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Error\Entity\ErrorWithoutChange;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Weight
{
    /**
     * @return string
     */
    public function weightEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function weightAnnotationOnly(): array
    {
        [$team, $leader] = $this->workTaskTeam();

        return [
            'id'     => true,
            'weight' => [
                'label'    => Helper::cnSpace(),
                'typeArgs' => $this->weightTypeArgs($leader),
                'style'    => ['margin-bottom' => '48px'],
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function weightFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setIcon('b:icon-jewelry')->setLabel('Update task weight');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return array|Error
     */
    public function weightAfterSubmit(Arguments $args)
    {
        if ($args->recordBefore['weight'] == $args->submit['weight']) {
            return new ErrorWithoutChange();
        }

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function weightAfterPersistence(Arguments $args)
    {
        $userId = $args->recordBefore['userId'];
        if ($this->usr('usr_uid') != $userId) {
            $this->sendTelegramTips(
                false,
                $userId,
                '{{ member }} change task {{ task }} weight to {{ to }}',
                [
                    '{{ to }}'   => $args->record['weight'],
                    '{{ task }}' => $args->recordBefore['title'],
                ]
            );
        }

        return $this->trailLogger(
            $args,
            $this->messageLang(
                'Change weight from {{ from }} to {{ to }}',
                [
                    '{{ from }}' => $args->recordBefore['weight'],
                    '{{ to }}'   => $args->record['weight'],
                ]
            )
        );
    }

    /**
     * Adjustment task weight
     *
     * @Route("/bsw-work-task/weight/{id}", name="app_bsw_work_task_weight", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function weight(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(
            [
                'id'   => $id,
                'sets' => ['function' => 'refreshPreview'],
            ]
        );
    }
}