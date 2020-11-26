<?php

namespace Leon\BswBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\FoundationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;
use Exception;

abstract class RecursionSqlCommand extends Command
{
    use BswFoundation;

    /**
     * @var ObjectRepository|FoundationRepository|ObjectManager|EntityRepository
     */
    protected $repo;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var array
     */
    protected $moment = [];

    /**
     * @var int
     */
    protected $limit = 500;

    /**
     * @var object
     */
    protected $_params;

    /**
     * @var object
     */
    protected $params;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var bool
     */
    protected $handlerByMultiple = false;

    /**
     * @var bool
     */
    protected $fixPagination = false;

    /**
     * @var int
     */
    protected $fpMaxId = 0;

    /**
     * @var int
     */
    protected $fpRecordTotal;

    /**
     * @var bool
     */
    protected $process = '[{Time}] -> page {PageNow}/{PageTotal}, round {RoundSuccess}/{RoundTotal}, total {RecordSuccess}/{RecordTotal}, process {Process}%';

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'limit'    => [null, InputOption::VALUE_OPTIONAL, 'Limit of list handler', $this->limit],
            'page'     => [null, InputOption::VALUE_OPTIONAL, 'Page of list handler', $this->page],
            'force'    => [null, InputOption::VALUE_OPTIONAL, 'Force command', 'no'],
            'args'     => [null, InputOption::VALUE_OPTIONAL, 'Extra arguments'],
            'receiver' => [null, InputOption::VALUE_OPTIONAL, 'Receiver telegram id, split by comma'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'sql',
            'info'    => 'Recursion execute sql',
        ];
    }

    /**
     * @return string
     */
    public function entity(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function extraFilter(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function filter(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function lister(): array
    {
        return [];
    }

    /**
     * @param object $params
     *
     * @return object
     */
    public function params($params)
    {
        return $params;
    }

    /**
     * @return bool
     */
    public function pass(): bool
    {
        if ($this->params->force == 'yes') {
            return true;
        }

        if (!empty($this->moment)) {
            foreach ($this->moment as $moment => $format) {
                if (date($format) == $moment) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $record
     *
     * @return int|bool
     */
    abstract public function handler(array $record);

    /**
     * @param int $page
     *
     * @return void
     */
    public function done(int $page)
    {
        $this->output->writeln("<info>\n Sql recursion done\n </info>");
    }

    /**
     * @param float $percent
     *
     * @return bool
     */
    public function percent(float $percent): bool
    {
        return true;
    }

    /**
     * @param int $limit
     * @param int $pageTotal
     * @param int $pageNow
     * @param int $roundTotal
     * @param int $roundSuccess
     * @param int $recordTotal
     * @param int $recordSuccess
     *
     * @return string|null
     */
    public function process(
        int $limit,
        int $pageTotal,
        int $pageNow,
        int $roundTotal,
        int $roundSuccess,
        int $recordTotal,
        int $recordSuccess
    ): ?string {

        $time = Helper::date();
        $recordDone = (($pageNow - 1) * $limit) + $roundTotal;
        $process = number_format($recordDone / $recordTotal * 100, 2);

        $info = str_replace(
            [
                '{Time}',
                '{Limit}',
                '{PageTotal}',
                '{PageNow}',
                '{RoundTotal}',
                '{RoundSuccess}',
                '{RecordTotal}',
                '{RecordSuccess}',
                '{Process}',
            ],
            [$time, $limit, $pageTotal, $pageNow, $roundTotal, $roundSuccess, $recordTotal, $recordSuccess, $process],
            $this->process
        );

        if ($this->percent($process)) {
            return "<info> {$info} </info>";
        }

        return null;
    }

    /**
     * Execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        if (method_exists($this, $fn = Abs::FN_INIT)) {
            $this->{$fn}();
        }

        $this->_params = $this->options($input);
        $this->params = (object)$this->_params;
        $this->params->args = (object)Helper::jsonArray64($this->params->args);
        $this->params = $this->params($this->params);

        if (!$this->pass()) {
            return;
        }

        $this->output->writeln(
            "<info>\n {$this->getName()} => " . static::class . " -> " . Helper::date() . "\n </info>"
        );

        $page = $this->logic($this->params->limit, $this->params->page);
        $this->done($page);
    }

    /**
     * @param int $limit
     * @param int $pageNow
     * @param int $recordSuccess
     *
     * @return int
     * @throws
     */
    protected function logic(int $limit, int $pageNow, int $recordSuccess = 0): int
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Arguments `limit` should be integer and gte 1');
        }

        $query = Helper::pageArgs(
            [
                'paging' => true,
                'page'   => $pageNow,
                'limit'  => $limit,
            ]
        );

        if ($entity = $this->entity()) {

            $this->alias = Helper::tableNameToAlias($entity);
            $this->repo = $this->repo($entity);

            $fixFilter = [];
            if ($this->fixPagination) {
                $pk = "{$this->alias}.{$this->repo->pk()}";
                $fixFilter['order'] = [$pk => Abs::SORT_ASC];
                if ($pageNow > 1) {
                    $query = array_merge($query, ['page' => 1, 'offset' => 0]);
                    $fixFilter = array_merge(
                        $fixFilter,
                        [
                            'where'  => [$this->expr->gt($pk, ':pk')],
                            'args'   => ['pk' => [$this->fpMaxId]],
                            'hint'   => $this->fpRecordTotal,
                            'offset' => 0,
                        ]
                    );
                }
            }

            $extraFilter = $this->extrafilter();
            if ($fixFilter) {
                array_push($extraFilter, $fixFilter);
            }

            $filter = array_merge($this->filter(), $query);
            $result = $this->repo->filters(...$extraFilter)->lister($filter);

        } elseif ($result = $this->lister()) {
            $result = $this->web->manualListForPagination($result, $query);
        } else {
            $result = [];
        }

        $this->params->page = $pageNow;
        if (empty($result['items'])) {
            return $pageNow === 1 ? 0 : $pageNow;
        }

        $roundSuccess = 0;
        $pageTotal = $result['total_page'];
        $recordTotal = $result['total_item'];

        try {
            if ($this->handlerByMultiple) {
                $total = $this->handler($result['items']);
                $roundSuccess += (is_bool($total) ? (int)$total : $total);
            } else {
                foreach ($result['items'] as $record) {
                    $roundSuccess += ($this->handler($record) ? 1 : 0);
                }
            }
        } catch (Exception $e) {
            $this->output->writeln("<error>\n {$e->getMessage()}\n </error>");

            return 0;
        }

        $recordSuccess += $roundSuccess;
        $roundTotal = count($result['items']);

        if ($this->fixPagination) {
            $this->fpMaxId = $result['items'][$roundTotal - 1][$this->repo->pk()];
            $this->fpRecordTotal = $recordTotal;
        }

        if ($this->process) {
            $this->output->writeln(
                $this->process($limit, $pageTotal, $pageNow, $roundTotal, $roundSuccess, $recordTotal, $recordSuccess)
            );
        }

        if ($limit == $roundTotal) {
            return $this->logic($limit, ++$pageNow, $recordSuccess);
        }

        return $pageNow;
    }
}
