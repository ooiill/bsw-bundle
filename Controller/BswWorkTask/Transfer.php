<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Error\Entity\ErrorWithoutChange;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait Transfer
{
    /**
     * @return string
     */
    public function transferEntity(): string
    {
        return BswWorkTask::class;
    }

    /**
     * @return array
     */
    public function transferAnnotationOnly(): array
    {
        return [
            'id'     => true,
            'userId' => true,
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function transferFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setIcon('b:icon-feng')->setLabel('Transfer task');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return array|Error
     */
    public function transferAfterSubmit(Arguments $args)
    {
        if ($args->submit['userId'] == $args->recordBefore['userId']) {
            return new ErrorWithoutChange();
        }

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function transferAfterPersistence(Arguments $args)
    {
        if ($this->usr('usr_uid') != $args->record['userId']) {
            $this->sendTelegramTips(
                false,
                $args->record['userId'],
                '{{ member }} transfer task {{ task }} to you',
                ['{{ task }}' => $args->recordBefore['title']]
            );
        }

        $user = $this->getUserById($args->record['userId']);

        return $this->trailLogger(
            $args,
            $this->messageLang(
                'Transfer task to {{ to }}',
                ['{{ to }}' => $user->name]
            )
        );
    }

    /**
     * Transfer task
     *
     * @Route("/bsw-work-task/transfer/{id}", name="app_bsw_work_task_transfer", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function transfer(int $id = null): Response
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