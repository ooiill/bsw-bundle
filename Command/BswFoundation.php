<?php

namespace Leon\BswBundle\Command;

use App\Kernel;
use Doctrine\ORM\Query\Expr;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswFreeController;
use Leon\BswBundle\Controller\Traits\Database;
use Leon\BswBundle\Entity\BswConfig;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;
use Predis\Client;
use Exception;

trait BswFoundation
{
    use ControllerTrait;
    use Database;
    use LockableTrait;

    /**
     * @var BswFreeController
     */
    protected $web;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var Expr
     */
    protected $expr;

    /**
     * BswFoundation constructor.
     *
     * @param BswFreeController   $web
     * @param ContainerInterface  $container
     * @param TranslatorInterface $translator
     * @param LoggerInterface     $logger
     */
    public function __construct(
        BswFreeController $web,
        ContainerInterface $container,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->web = $web;
        $this->container = $container;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->kernel = $this->container->get('kernel');
        $this->expr = new Expr();

        ini_set('date.timezone', 'PRC');
        ini_set('memory_limit', '2048M');
        ini_set('xdebug.max_nesting_level', 2048);

        parent::__construct();
    }

    /**
     * @return array
     */
    abstract function base(): array;

    /**
     * @return array
     */
    abstract function args(): array;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    abstract public function execute(InputInterface $input, OutputInterface $output);

    /**
     * Configure
     *
     * @throws
     */
    protected function configure()
    {
        $base = $this->base();

        $prefix = $base['prefix'] ?? 'no-prefix';
        $keyword = $base['keyword'] ?? 'no-keyword';
        $info = $base['info'] ?? 'no-info';

        $this->setName("{$prefix}:{$keyword}");
        $this->setDescription($info);

        foreach ($this->args() as $name => $item) {

            $key = "{$prefix}_{$keyword}_{$name}";
            if (!is_null($cnf = $this->config($key))) {
                $item[3] = $cnf;
            }
            $this->addOption($name, ...$item);
        }
    }

    /**
     * Get options
     *
     * @param InputInterface $input
     *
     * @return array
     */
    protected function options(InputInterface $input): array
    {
        $options = $input->getOptions();
        $keys = array_keys($this->args());

        return Helper::arrayPull($options, $keys, false, '');
    }

    /**
     * Get yml/db config
     *
     * @param string $key
     *
     * @return mixed
     * @throws
     */
    protected function config(string $key = null)
    {
        $config = $this->web->caching(
            function () {
                $config = $this->web->parameters('cnf');
                try {
                    $dbConfig = $this->repo(BswConfig::class)->kvp(['value'], 'key');
                } catch (Exception $e) {
                    $dbConfig = [];
                }

                return (object)array_merge($config, $dbConfig);
            }
        );

        if (isset($key)) {
            return $config->{$key} ?? null;
        }

        return $config;
    }

    /**
     * Get redis instance
     *
     * @param string $name
     * @param int    $database
     *
     * @return Client
     */
    protected function redis(string $name = 'common', int $database = 0): Client
    {
        /**
         * @var Client $redis
         */
        $redis = $this->container->get("snc_redis.{$name}");
        $redis->select($database);

        return $redis;
    }
}