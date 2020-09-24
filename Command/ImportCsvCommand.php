<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Csv;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;
use Exception;

abstract class ImportCsvCommand extends Command
{
    use BswFoundation;

    /**
     * @var object
     */
    protected $_params;

    /**
     * @var object
     */
    protected $params;

    /**
     * @var OutputInterface
     */
    protected $output;

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
            'csv'       => [null, InputOption::VALUE_REQUIRED, 'The csv file'],
            'limit'     => [null, InputOption::VALUE_OPTIONAL, 'Limit of list handler', 100],
            'data-line' => [null, InputOption::VALUE_OPTIONAL, 'The line number of data', 2],
            'args'      => [null, InputOption::VALUE_OPTIONAL, 'Extra arguments'],
            'receiver'  => [null, InputOption::VALUE_OPTIONAL, 'Receiver telegram id, split by comma'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'import-csv',
            'info'    => 'Import from csv',
        ];
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
        $this->output->writeln("<info>\n Csv import done\n </info>");
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
     * @return string
     */
    public function process(
        int $limit,
        int $pageTotal,
        int $pageNow,
        int $roundTotal,
        int $roundSuccess,
        int $recordTotal,
        int $recordSuccess
    ): string {

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

        if ($roundSuccess == 0) {
            $type = '<fg=red> %s </>';
        } elseif ($roundSuccess < $roundTotal) {
            $type = '<fg=yellow> %s </>';
        } else {
            $type = '<fg=green> %s </>';
        }

        return sprintf($type, $info);
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

        $this->params->{'data-line'} -= 1;

        $this->output->writeln(
            "<info>\n {$this->getName()} => " . static::class . " -> " . Helper::date() . "\n </info>"
        );

        $page = $this->logic($this->params->limit, $this->params->csv);
        $this->done($page);
    }

    /**
     * Csv reader
     *
     * @param string $csv
     * @param int    $page
     * @param int    $limit
     *
     * @return array
     * @throws
     */
    protected function csvReader(string $csv, int $page, int $limit): array
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Csv();
            $instance->setCsvFile($csv);
        }

        $total = $instance->lines() + 1;
        $args = Helper::pageArgs(['page' => $page, 'limit' => $limit]);
        $items = $instance->reader($args['limit'], $args['offset'] + $this->params->{'data-line'}, false);

        // filter blank line
        $items = array_map('array_filter', $items);
        $items = array_filter($items);

        return [$total - $this->params->{'data-line'} - 1, $items];
    }

    /**
     * @param int    $limit
     * @param string $csv
     * @param int    $pageNow
     * @param int    $recordSuccess
     *
     * @return int
     * @throws
     */
    protected function logic(int $limit, string $csv, int $pageNow = 1, int $recordSuccess = 0): int
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Arguments `limit` should be integer and gte 1');
        }

        [$recordTotal, $items] = $this->csvReader($csv, $pageNow, $limit);
        if (empty($items)) {
            return $pageNow === 1 ? 0 : $pageNow;
        }

        $roundSuccess = 0;
        $pageTotal = ceil($recordTotal / $limit);

        try {
            foreach ($items as $record) {
                $record = Helper::numericValues($record);
                $roundSuccess += ($this->handler($record) ? 1 : 0);
            }
        } catch (Exception $e) {
            $this->output->writeln("<error>\n {$e->getMessage()}\n </error>");

            return 0;
        }

        $recordSuccess += $roundSuccess;
        $roundTotal = count($items);

        if ($this->process) {
            $this->output->writeln(
                $this->process($limit, $pageTotal, $pageNow, $roundTotal, $roundSuccess, $recordTotal, $recordSuccess)
            );
        }

        if ($limit == $roundTotal) {
            return $this->logic($limit, $csv, ++$pageNow, $recordSuccess);
        }

        return $pageNow;
    }
}