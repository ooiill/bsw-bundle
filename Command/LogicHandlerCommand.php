<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class LogicHandlerCommand extends Command
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
            'force' => [null, InputOption::VALUE_OPTIONAL, 'Force command', 'no'],
            'args'  => [null, InputOption::VALUE_OPTIONAL, 'Extra arguments'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'logic',
            'info'    => 'Logic handler',
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
     * @return void
     */
    abstract public function handler();

    /**
     * @return void
     */
    public function done()
    {
        $this->output->writeln("<info>\n Logic handle done\n </info>");
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

        $this->handler();
        $this->done();
    }
}
