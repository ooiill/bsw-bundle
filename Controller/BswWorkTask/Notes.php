<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Repository\BswWorkTaskRepository;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait Notes
{
    /**
     * @return string
     */
    public function notesEntity(): string
    {
        return BswWorkTaskTrail::class;
    }

    /**
     * @return array
     */
    public function notesAnnotationOnly(): array
    {
        return [
            'id'     => false,
            'userId' => ['show' => false],
            'taskId' => ['hide' => true],
            'trail'  => [
                'label'    => 'Notes',
                'typeArgs' => [
                    'minRows' => $this->isTeamTask ? 10 : 5,
                    'maxRows' => $this->isTeamTask ? 10 : 5,
                ],
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function notesFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setIcon('b:icon-form')->setLabel('Write notes');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return array|Error
     */
    public function notesAfterSubmit(Arguments $args)
    {
        $args->submit['userId'] = $this->usr('usr_uid');
        $args->submit['trail'] = $this->messageLang(
            'Write task notes, {{ notes }}',
            ['{{ notes }}' => $args->submit['trail']]
        );

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function notesAfterPersistence(Arguments $args)
    {
        /**
         * @var BswWorkTaskRepository $taskRepo
         */
        $taskRepo = $this->repo(BswWorkTask::class);
        $task = $taskRepo->find($args->record['taskId']);

        if ($this->usr('usr_uid') != $task->userId) {
            $this->sendTelegramTips(
                false,
                $task->userId,
                '{{ member }} write notes in {{ task }}, {{ notes }}',
                [
                    '{{ task }}'  => $task->title,
                    '{{ notes }}' => $args->record['trail'],
                ]
            );
        }

        return true;
    }

    /**
     * Write task notes
     *
     * @Route("/bsw-work-task/notes/{id}", name="app_bsw_work_task_notes", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function notes(int $id = null): Response
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