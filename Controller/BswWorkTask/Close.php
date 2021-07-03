<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Repository\BswWorkTaskRepository;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Close
{
    /**
     * @return string
     */
    public function closeEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @return array
     */
    public function closeAnnotationOnly(): array
    {
        return [
            'id'    => true,
            'state' => true,
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function closeAfterPersistence(Arguments $args)
    {
        /**
         * @var BswWorkTaskRepository $taskRepo
         */
        $taskRepo = $this->repo(BswWorkTask::class);
        $taskId = $args->newly ? $args->result : $args->original['id'];
        $task = $taskRepo->find($taskId);

        if ($this->usr('usr_uid') != $task->userId) {
            $this->sendTelegramTips(
                false,
                $task->userId,
                '{{ member }} close task {{ task }}',
                ['{{ task }}' => $task->title]
            );
        }

        return $this->trailLogger($args, $this->messageLang('Close the task'));
    }

    /**
     * Close task
     *
     * @Route("/bsw-work-task/close/{id}", name="app_bsw_work_task_close", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function close(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(
            [
                'id'        => $id,
                'submit'    => ['id' => $id, 'state' => 0],
                'nextRoute' => null,
            ]
        );
    }
}