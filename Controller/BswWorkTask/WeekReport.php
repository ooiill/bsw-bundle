<?php

namespace Leon\BswBundle\Controller\BswWorkTask;

use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Entity\BswWorkTask;
use Leon\BswBundle\Entity\BswWorkTaskTrail;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Entity\Between;
use Leon\BswBundle\Module\Filter\Entity\TeamMember;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\SelectTree;
use Leon\BswBundle\Module\Form\Entity\Week;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr;

/**
 * @property Expr $expr
 */
trait WeekReport
{
    /**
     * @var array
     */
    protected $weekReportAlias = [
        'team'   => ['u', 'teamId'],
        'member' => ['tt', 'userId'],
    ];

    /**
     * @return string
     */
    public function weekReportEntity(): string
    {
        return BswWorkTaskTrail::class;
    }

    /**
     * @return array
     */
    public function weekReportFilterAnnotationOnly(): array
    {
        [$team] = $this->workTaskTeam();

        return [
            'team' => [
                'label'      => 'User id',
                'field'      => 'u.teamId',
                'type'       => SelectTree::class,
                'typeArgs'   => ['expandAll' => true],
                'enum'       => $this->getTeamMemberTree($team),
                'filter'     => TeamMember::class,
                'filterArgs' => ['alias' => $this->weekReportAlias],
                'value'      => $this->teamDefaultValue(),
                'column'     => 3,
            ],
            'week' => [
                'label'      => 'Week n',
                'field'      => 'tt.addTime',
                'type'       => Week::class,
                'filter'     => Between::class,
                'filterArgs' => ['weekValue' => true, 'carryTime' => false],
                'value'      => date('Y-W'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function weekReportQuery(): array
    {
        return [
            'paging' => false,
            'select' => ['u.name', 't.title', 't.type', 'tt.trail', 'tt.addTime AS time'],
            'alias'  => 'tt',
            'join'   => [
                't' => [
                    'entity' => BswWorkTask::class,
                    'left'   => ['tt.taskId'],
                    'right'  => ['t.id'],
                ],
                'u' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['tt.userId'],
                    'right'  => ['u.id'],
                ],
            ],
            'where'  => [
                $this->expr->eq('tt.reliable', ':reliable'),
                $this->expr->eq('tt.state', ':state'),
            ],
            'args'   => [
                'reliable' => [1],
                'state'    => [Abs::NORMAL],
            ],
            'sort'   => ['tt.id' => Abs::SORT_ASC],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function weekReportFilterCorrect(Arguments $args): array
    {
        $args->condition = $this->correctTeamMemberFilter(
            'u.teamId',
            $this->weekReportAlias,
            $args->condition ?? null
        );

        return [$args->filter, $args->condition];
    }

    /**
     * @return Button[]
     */
    public function weekReportOperates()
    {
        return $this->operatesButton();
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function weekReportAfterHook(Arguments $args)
    {
        $args->hooked = current($this->taskTrailHandler([$args->hooked]));

        return $args->hooked;
    }

    /**
     * Week report
     *
     * @Route("/bsw-work-week-report", name="app_bsw_work_week_report")
     * @Access()
     *
     * @return Response
     */
    public function weekReport(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview([], [], 'task/week-report.html');
    }
}