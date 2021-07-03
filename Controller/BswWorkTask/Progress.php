<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Error\Entity\ErrorAccess;
use Leon\BswBundle\Module\Error\Entity\ErrorProgress;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Mentions;
use Leon\BswBundle\Repository\BswAdminUserRepository;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait Progress
{
    /**
     * @return string
     */
    public function progressEntity(): string
    {
        return $this->persistenceEntity();
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function progressEnumExtraWhatTodo(Arguments $args)
    {
        [$team] = $this->workTaskTeam();

        /**
         * @var BswAdminUserRepository $adminRepo
         */
        $adminRepo = $this->repo(BswAdminUser::class);

        return $adminRepo->kvp(
            ['name'],
            'telegramId',
            null,
            [
                'where' => [
                    $this->expr->gt('kvp.telegramId', ':telegram'),
                    $this->expr->eq('kvp.teamId', ':team'),
                ],
                'args'  => [
                    'telegram' => [0],
                    'team'     => [$team],
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public function progressAnnotationOnly(): array
    {
        return [
            'id'          => true,
            'donePercent' => [
                'label'    => Helper::cnSpace(),
                'typeArgs' => [
                    'step' => 5,
                ],
            ],
            'whatToDo'    => [
                'type'      => Mentions::class,
                'enumExtra' => true,
                'typeArgs'  => ['rows' => 6],
                'formRules' => [$this->formRuleRequired()],
            ],
            'state'       => ['show' => false],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function progressFormOperates(Arguments $args): array
    {
        /**
         * @var Button $submit
         */
        $submit = $args->submit;
        $submit->setIcon('b:icon-process')->setLabel('Update task progress');

        return compact('submit');
    }

    /**
     * @param Arguments $args
     *
     * @return Error|array
     */
    public function progressAfterSubmit(Arguments $args)
    {
        if ($args->recordBefore['donePercent'] >= $args->submit['donePercent']) {
            return new ErrorProgress();
        }

        [$myTeam, $leader] = $this->workTaskTeam();
        $userTeam = $this->getUserById($args->recordBefore['userId'])->teamId;

        if (
            ($args->recordBefore['userId'] !== $this->usr('usr_uid')) &&
            !($leader && ($myTeam === $userTeam))
        ) {
            return new ErrorAccess();
        }

        if ($args->submit['donePercent'] <= 0) {
            $args->submit['state'] = 1;
        } elseif ($args->submit['donePercent'] < 100) {
            $args->submit['state'] = 2;
        } else {
            if (time() <= $args->recordBefore['endTime']) {
                $args->submit['state'] = 3;
            } else {
                $args->submit['state'] = 4;
            }
        }

        return [$args->submit, $args->extraSubmit];
    }

    /**
     * @param Arguments $args
     *
     * @return bool|Error
     */
    public function progressAfterPersistence(Arguments $args)
    {
        $member = $this->matchMentions($args->extraSubmit['whatToDo']);
        foreach ($member as $item) {
            $this->sendTelegramTips(
                true,
                $item['telegramId'],
                '{{ member }} mentions you in task {{ task }} {{ mention }}',
                [
                    '{{ task }}'    => $args->recordBefore['title'],
                    '{{ mention }}' => "[{$item['name']}](tg://user?id={$item['telegramId']})",
                ]
            );
        }

        [$team, $leader, $leaderId, $leaderTg] = $this->workTaskTeamAndLeader();
        if ($this->usr('usr_uid') != $leaderId) {
            $this->sendTelegramTips(
                true,
                $leaderTg,
                '{{ member }} change task {{ task }} progress from {{ from }} to {{ to }}, {{ remark }}',
                [
                    '{{ task }}'   => $args->recordBefore['title'],
                    '{{ from }}'   => $args->recordBefore['donePercent'],
                    '{{ to }}'     => $args->record['donePercent'],
                    '{{ remark }}' => $args->extraSubmit['whatToDo'],
                ]
            );
        }

        return $this->trailLogger(
            $args,
            $this->messageLang(
                'Change progress from {{ from }} to {{ to }}, {{ remark }}',
                [
                    '{{ from }}'   => $args->recordBefore['donePercent'],
                    '{{ to }}'     => $args->record['donePercent'],
                    '{{ remark }}' => $args->extraSubmit['whatToDo'],
                ]
            ),
            true
        );
    }

    /**
     * Adjustment task progress
     *
     * @Route("/bsw-work-task/progress/{id}", name="app_bsw_work_task_progress", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function progress(int $id = null): Response
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