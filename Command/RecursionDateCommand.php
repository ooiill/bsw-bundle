<?php

namespace Leon\BswBundle\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\FoundationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

abstract class RecursionDateCommand extends Command
{
    use BswFoundation;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var array
     */
    protected $moment = [];

    /**
     * @var object
     */
    protected $_params;

    /**
     * @var object
     */
    protected $params;

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'force'      => [null, InputOption::VALUE_OPTIONAL, 'Force command', 'no'],
            'args'       => [null, InputOption::VALUE_OPTIONAL, 'Extra arguments'],
            'begin-date' => [
                null,
                InputOption::VALUE_OPTIONAL,
                'Begin the date, if not configured it be yesterday',
            ],
            'end-date'   => [
                null,
                InputOption::VALUE_OPTIONAL,
                'End the date (including it), if not configured it be yesterday',
            ],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'date',
            'info'    => 'Recursion date and execute something',
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
     * @param string $date
     *
     * @return void
     */
    abstract public function handler(string $date);

    /**
     * @param string $date
     *
     * @return void
     */
    public function done(string $date)
    {
        $this->output->writeln("<info>\n Date ({$date}) recursion done\n </info>");
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
        if (is_string($this->params->args)) {
            $this->params->args = (object)Helper::jsonArray64($this->params->args);
        }
        $this->params = $this->params($this->params);

        if (!$this->pass()) {
            return;
        }

        if (empty($this->params->{'begin-date'})) {
            $this->params->{'begin-date'} = Helper::dateDayDiff(-1);
        }
        if (empty($this->params->{'end-date'})) {
            $this->params->{'end-date'} = Helper::dateDayDiff(-1);
        }
        if (Helper::compareDateTime($this->params->{'begin-date'}, $this->params->{'end-date'}) == 1) {
            return;
        }

        $this->output->writeln(
            "<info>\n {$this->getName()} => " . static::class . " -> " . Helper::date() . "\n </info>"
        );

        try {
            $this->handler($this->params->{'begin-date'});
        } catch (Exception $e) {
            $this->output->writeln("<error>\n {$e->getMessage()}\n </error>");

            return;
        }

        $this->done($this->params->{'begin-date'});

        // next day
        $this->params->{'begin-date'} = Helper::dateDayDiff(1, $this->params->{'begin-date'});
        $arguments = Helper::objectToArray($this->params);
        $this->web->commandCaller($this->getName(), $arguments, $output);
    }
}
