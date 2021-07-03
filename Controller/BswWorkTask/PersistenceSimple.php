<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait PersistenceSimple
{
    /**
     * @return string
     */
    public function simpleEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function simpleAnnotation(Arguments $args): array
    {
        $annotation = $this->persistenceAnnotation($args);
        $annotation = array_merge(
            $annotation,
            [
                'type'        => ['hide' => true],
                'userId'      => false,
                'donePercent' => false,
                'weight'      => false,
                'remark'      => false,
                'state'       => false,
            ]
        );

        return $annotation;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function simpleFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setIcon('a:bug')->setLabel('New task');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return Message|array
     */
    public function simpleAfterSubmit(Arguments $args)
    {
        $result = $this->persistenceAfterSubmit($args);
        if (!is_array($result)) {
            return $result;
        }

        [$submit, $extra] = $result;
        $submit['userId'] = $this->usr('usr_uid');

        return [$submit, $extra];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function simpleAfterPersistence(Arguments $args)
    {
        [$team, $leader, $leaderId, $leaderTg] = $this->workTaskTeamAndLeader();

        if ($this->usr('usr_uid') != $leaderId) {
            $this->sendTelegramTips(
                true,
                $leaderTg,
                '{{ member }} create task {{ task }} for self',
                ['{{ task }}' => $args->record['title']]
            );
        }

        return $this->trailLogger($args, $this->messageLang('Create the task'));
    }

    /**
     * Add task
     *
     * @Route("/bsw-work-task/simple/{id}", name="app_bsw_work_task_simple", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function simple(int $id = null): Response
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