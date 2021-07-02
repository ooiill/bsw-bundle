<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\CommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route as RoutingRoute;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class BswInitCommand extends Command
{
    use BswFoundation;

    /**
     * @var string
     */
    protected $project;

    /**
     * @var string
     */
    protected $app;

    /**
     * @return array
     */
    public function args(): array
    {
        $opt = InputOption::VALUE_OPTIONAL;

        return [
            'doctrine'            => [null, $opt, 'Doctrine database flag'],
            'doctrine-is-default' => [null, $opt, 'Doctrine is default doctrine', 'yes'],
            'force'               => [null, $opt, 'Force init again', 'no'],
            'app'                 => [null, $opt, 'App flag for scaffold suffix', 'backend'],
            'project'             => [null, $opt, 'App name for config', 'customer'],
            'scheme-prefix'       => [null, $opt, 'Bsw scheme prefix'],
            'scheme-prefix-mode'  => [null, $opt, 'Bsw scheme prefix mode add or remove', 'add'],
            'scheme-bsw'          => [null, $opt, 'Bsw scheme required?', 'yes'],
            'scheme-extra'        => [null, $opt, 'Extra scheme path'],
            'scheme-only'         => [null, $opt, 'Only scheme split by comma'],
            'scheme-start-only'   => [null, $opt, 'Only scheme start with string'],
            'scheme-force'        => [null, $opt, 'Force rebuild scheme', 'no'],
            'scheme-reverse'      => [null, $opt, 'Reverse scheme split by comma, * for all'],
            'scaffold-need'       => [null, $opt, 'Scaffold need?', 'yes'],
            'scaffold-cover'      => [null, $opt, 'Scaffold file rewrite?', 12],
            'scaffold-path'       => [null, $opt, 'Scaffold file save path'],
            'scaffold-ns'         => [null, $opt, 'Scaffold namespace for MVC class'],
            'config-need'         => [null, $opt, 'Config need?', 'yes'],
            'document-need'       => [null, $opt, 'Document need?', 'yes'],
            'directory'           => [null, $opt, 'The directory where is scaffold template'],
            'comment-2-label'     => [null, $opt, 'Fields comment to label', 'no'],
            'comment-2-menu'      => [null, $opt, 'Tables comment to menu', 'no'],
            'acme'                => [null, $opt, 'Acme controller class for annotation hint'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'init',
            'info'    => 'Project initialization',
        ];
    }

    /**
     * @return array
     */
    protected function devJmsSerializerCnf(): array
    {
        return [
            'jms_serializer' => [
                'visitors' => [
                    'json' => [
                        'options' => [
                            'JSON_PRETTY_PRINT',
                            'JSON_UNESCAPED_SLASHES',
                            'JSON_PRESERVE_ZERO_FRACTION',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function prodJmsSerializerCnf(): array
    {
        return [
            'jms_serializer' => [
                'visitors' => [
                    'json' => [
                        'options' => [
                            'JSON_UNESCAPED_SLASHES',
                            'JSON_PRESERVE_ZERO_FRACTION',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function cacheCnf(): array
    {
        return [
            'framework' => [
                'cache' => [
                    'app'                    => 'cache.adapter.redis',
                    'default_redis_provider' => '%env(REDIS_URL)%',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function fosRestCnf(): array
    {
        if ($this->app !== Abs::APP_TYPE_API) {
            return ['fos_rest' => null];
        }

        return [
            'fos_rest' => [
                'service'         => ['serializer' => null],
                'routing_loader'  => ['default_format' => 'json'],
                'format_listener' => [
                    'rules' => [
                        [
                            'path'             => '^/',
                            'prefer_extension' => true,
                            'fallback_format'  => 'json',
                            'priorities'       => ['json'],
                        ],
                    ],
                ],
                'exception'       => [
                    'exception_controller' => 'App\Controller\AcmeApiController::showExceptionAction',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function frameworkCnf(): array
    {
        return [
            'framework' => [
                'session'         => [
                    'gc_maxlifetime'  => 86400,
                    'cookie_lifetime' => 86400,
                    'cookie_secure'   => 'auto',
                    'cookie_samesite' => 'lax',
                ],
                'csrf_protection' => true,
                'ide'             => 'phpstorm://open?file=%%f&line=%%l',
                'secret'          => '%env(APP_SECRET)%',
                'php_errors'      => ['log' => true],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function jmsSerializerCnf(): array
    {
        return [
            'jms_serializer' => [
                'visitors' => [
                    'json' => [
                        'options' => [
                            'JSON_PRETTY_PRINT',
                            'JSON_UNESCAPED_UNICODE',
                        ],
                    ],
                    'xml'  => ['format_output' => '%kernel.debug%'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function sncRedisCnf(): array
    {
        return [
            'snc_redis' => [
                'clients' => [
                    'default' => [
                        'type'  => 'predis',
                        'alias' => 'default',
                        'dsn'   => '%env(REDIS_URL)%',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function translationCnf(): array
    {
        return [
            'framework' => [
                'default_locale' => '%locale%',
                'translator'     => [
                    'default_path' => '%kernel.project_dir%/translations',
                    'fallbacks'    => ['en'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function twigCnf(): array
    {
        $twig = [
            'twig' => [
                'paths'                => [
                    '%kernel.project_dir%/templates',
                    '%kernel.project_dir%/vendor/ooiill/bsw-bundle/Resources/views',
                ],
                'default_path'         => '%kernel.project_dir%/templates',
                'debug'                => '%kernel.debug%',
                'strict_variables'     => '%kernel.debug%',
                'exception_controller' => 'Leon\BswBundle\Controller\BswBackendController::showExceptionAction',
            ],
        ];
        if ($this->app == Abs::APP_TYPE_BACKEND) {
            array_unshift($twig['twig']['paths'], '%kernel.project_dir%/templates-terse');
        }

        return $twig;
    }

    /**
     * @return array
     */
    protected function annotationCnf(): array
    {
        return [
            'controllers' => [
                'resource' => '../../src/Controller/',
                'type'     => 'annotation',
            ],
            'kernel'      => [
                'resource' => '../../src/Kernel.php',
                'type'     => 'annotation',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function routesCnf(): array
    {
        if ($this->app == Abs::APP_TYPE_BACKEND) {
            /*
            return [
                'leon_bsw_bundle' => [
                    'resource' => '@' . Abs::BSW_BUNDLE . '/Controller',
                    'type'     => 'annotation',
                ],
            ];
            */

            $routesYaml = [];
            $routes = $this->web->getRouteCollection();

            $extraRoutes = [
                'app_clean_backend',
                'app_clean_project',
                'app_export',
                'app_language',
                'app_theme',
                'app_skin',
                'app_captcha',
                'app_site_index',
                'app_third_message',
            ];

            foreach ($routes as $item) {

                $id = $item['route'];
                if (!(
                    strpos($id, 'app_bsw_') === 0 ||
                    strpos($id, 'app_tg_') === 0 ||
                    in_array($id, $extraRoutes)
                )) {
                    continue;
                }

                /**
                 * @var RoutingRoute $instance
                 */
                $instance = $item['instance'];

                $requirements = $instance->getRequirements();
                $defaults = $instance->getDefaults();
                Helper::arrayPop($defaults, ['_controller']);

                $path = $item['path'];
                if (strpos($path, 'BackendCover') === false) {
                    $path = str_replace(
                        ['\\Controller', '\\Acme'],
                        ['\\Controller\\BackendCover', null],
                        $path
                    );
                }

                $routesYaml[$id] = [
                    'path'       => $item['uri'],
                    'controller' => $path,
                ];

                if (!empty($requirements)) {
                    $routesYaml[$id]['requirements'] = $requirements;
                }

                if (!empty($defaults)) {
                    $routesYaml[$id]['defaults'] = $defaults;
                }
            }

            return $routesYaml;
        }

        return [
            'app_captcha' => [
                'path'       => '/captcha',
                'controller' => 'Leon\BswBundle\Controller\BswMixed\Acme::numberCaptcha',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function servicesCnf(): array
    {
        $signSalt = Helper::randString(16, 'mixed');
        $aesKey = Helper::randString(16, 'mixed');
        $debugDevil = Helper::randString(16, 'mixed');

        $project = Helper::underToCamel(str_replace("-", "_", $this->project), false);
        $routeDefault = [
            Abs::APP_TYPE_API      => 'api_welcome',
            Abs::APP_TYPE_WEB      => 'web_homepage',
            Abs::APP_TYPE_FRONTEND => 'web_homepage',
            Abs::APP_TYPE_BACKEND  => 'backend_homepage',
        ];

        return [
            'parameters' => [
                'locale'                     => 'cn',
                'version'                    => '1.0.0',
                'salt'                       => $signSalt,
                'salt_service'               => '',
                'platform_sms'               => 'aws',
                'platform_email'             => 'aws',
                'telegram_bot_token'         => '',
                'telegram_hooks_host'        => '',
                'backend_with_google_secret' => false,
                'backend_maintain_alone'     => false,
                'aes_key'                    => $aesKey,
                'aes_iv'                     => $aesKey,
                'aes_method'                 => 'AES-128-CBC',
                'jwt_issuer'                 => 'jwt-issuer',
                'jwt_type'                   => 'hmac',
                'bd_dwz_token'               => 'baidu-dwz-token',
                'ali_key'                    => 'ali-key',
                'ali_secret'                 => 'ali-secret',
                'ali_sms_key'                => '',
                'ali_sms_secret'             => '',
                'ali_sms_region'             => 'ali-sms-region',
                'ali_oss_key'                => '',
                'ali_oss_secret'             => '',
                'ali_oss_bucket'             => 'ali-oss-bucket',
                'ali_oss_endpoint'           => 'ali-oss-endpoint',
                'tx_key'                     => 'tx-key',
                'tx_secret'                  => 'tx-secret',
                'tx_sms_key'                 => '',
                'tx_sms_secret'              => '',
                'aws_region'                 => 'aws-region',
                'aws_key'                    => 'aws-key',
                'aws_secret'                 => 'aws-secret',
                'aws_email'                  => 'aws-sender@gmail.com',
                'smtp_host'                  => 'smtp.qq.com',
                'smtp_port'                  => 587,
                'smtp_sender'                => 'smtp-sender@qq.com',
                'smtp_secret'                => 'smtp-secret',
                'component'                  => [],
                'wx_official_default'        => [
                    'app_id'        => 'app-id',
                    'secret'        => 'secret',
                    'token'         => 'token',
                    'aes_key'       => 'aes-key',
                    'response_type' => 'object',
                    'oauth'         => [
                        'scopes'   => ['snsapi_userinfo'],
                        'callback' => '/wx/oauth',
                    ],
                ],
                'wx_payment_default'         => [
                    'app_id'    => 'app-id',
                    'mch_id'    => 'mch-id',
                    'key'       => 'key-for-signature',
                    'cert_path' => 'path/to/your/cert.pem',
                    'key_path'  => 'path/to/your/key',
                ],
                'ali_payment_default'        => [
                    'app_id'         => 'app-id',
                    'ali_public_key' => 'public-key-string',
                    'private_key'    => 'private-key-string',
                ],
                'cnf'                        => [
                    'app_logo'              => '/img/logo.svg',
                    'app_ico'               => '/img/logo.svg',
                    'app_name'              => $project,
                    'host'                  => '%env(resolve:APP_HOST)%',
                    'host_official'         => '%env(resolve:APP_HOST_OFFICIAL)%',
                    'host_file'             => '%env(resolve:APP_HOST_FILE)%',
                    'host_service'          => '%env(resolve:APP_HOST_SERVICE)%',
                    'cache_default_expires' => '%env(resolve:APP_CACHE_DEFAULT_EXPIRES)%',
                    'debug_devil'           => $debugDevil,
                    'debug_uuid'            => '_',
                    'debug_cost'            => true,
                    'route_default'         => $routeDefault[$this->app],
                    'login_container_class' => 'login-container',
                ],
            ],
            'services'   => [],
        ];
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
        $params = $this->options($input);
        $project = $this->kernel->getProjectDir();
        $dumper = new Dumper();

        $this->project = $params['project'];
        $this->app = $params['app'];

        $doneFile = "{$project}/.done-init";
        if ($params['force'] !== 'yes' && file_exists($doneFile)) {
            throw new CommandException('The command can only be executed once');
        }

        /**
         * Config
         */
        $config = [];
        if ($params['config-need'] === 'yes') {
            $config = [
                'devJmsSerializer'  => "{$project}/config/packages/dev/jms_serializer.yaml",
                'prodJmsSerializer' => "{$project}/config/packages/prod/jms_serializer.yaml",
                'cache'             => "{$project}/config/packages/cache.yaml",
                'fosRest'           => "{$project}/config/packages/fos_rest.yaml",
                'framework'         => "{$project}/config/packages/framework.yaml",
                'jmsSerializer'     => "{$project}/config/packages/jms_serializer.yaml",
                'sncRedis'          => "{$project}/config/packages/snc_redis.yaml",
                'translation'       => "{$project}/config/packages/translation.yaml",
                'twig'              => "{$project}/config/packages/twig.yaml",
                'annotation'        => "{$project}/config/routes/annotations.yaml",
                'routes'            => "{$project}/config/routes.yaml",
                'services'          => "{$project}/config/services.yaml",
            ];
        }

        foreach ($config as $name => $file) {
            $fileContent = Yaml::parseFile($file, Yaml::PARSE_CONSTANT) ?? [];
            $customContent = $this->{"{$name}Cnf"}();
            if (empty($customContent)) {
                continue;
            }

            $content = Helper::mergeWeak(true, false, true, $customContent, $fileContent);
            file_put_contents($file, $dumper->dump($content, 4, 0, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));

            $output->writeln("<info>  [Merge] {$file} </info>");
        }

        /**
         * Document
         */
        $documentFileList = [];
        if ($params['document-need'] === 'yes') {
            Helper::directoryIterator(__DIR__ . '/document', $documentFileList);
            $documentFileList = Helper::multipleToOne($documentFileList);
        }

        foreach ($documentFileList as $file) {
            $targetFile = str_replace(__DIR__, $project, $file);
            @mkdir(pathinfo($targetFile, PATHINFO_DIRNAME), 0755, true);

            if (strpos($targetFile, '.rst') !== false && file_exists($targetFile)) {
                continue;
            }
            copy($file, $targetFile);
            $output->writeln("<info>   [Copy] {$targetFile} </info>");
        }

        /**
         * Table scheme
         */
        $schemeFileList = [];
        $paths = ($params['scheme-bsw'] == 'yes' ? (__DIR__ . '/scheme') : null);
        $schemePath = explode(PATH_SEPARATOR, $paths . PATH_SEPARATOR . $params['scheme-extra']);

        foreach ($schemePath as $path) {
            Helper::directoryIterator(
                $path,
                $schemeFileList,
                function ($file) {
                    return strpos($file, '.sql') === false ? false : $file;
                }
            );
        }

        $pdo = $this->pdo($params['doctrine'] ?: Abs::DOCTRINE_DEFAULT);
        $database = $pdo->getDatabase();

        $schemeOnly = Helper::stringToArray($params['scheme-only']);
        $schemeStartOnly = $params['scheme-start-only'];
        $scaffoldNeed = ($params['scaffold-need'] === 'yes');
        $schemePrefix = trim($params['scheme-prefix'], '_');

        // for build scheme file (.sql)
        if ($params['scheme-reverse'] == '*') {
            $field = "Tables_in_{$database}";
            $tables = $pdo->fetchAllAssociative("SHOW TABLES WHERE {$field} NOT LIKE 'bsw_%'");
            $schemeReverse = array_column($tables, $field);
        } else {
            $schemeReverse = Helper::stringToArray($params['scheme-reverse']);
        }

        foreach ($schemeReverse as $table) {
            $output->write(Abs::ENTER);
            $scheme = $pdo->fetchArray("SHOW CREATE TABLE {$table}")[1];
            $scheme = str_replace(
                "CREATE TABLE `{$table}`",
                "CREATE TABLE `{TABLE_NAME}`",
                $scheme
            );

            $scheme = preg_replace("/AUTO_INCREMENT=([\d]+)\ /i", null, $scheme);
            if (empty($params['scheme-extra']) || !is_dir($params['scheme-extra'])) {
                $output->writeln("<error> Reverse:  [CreateFile] {$table} </error>");
                continue;
            }

            $sqlFile = "{$params['scheme-extra']}/{$table}.sql";
            array_push($schemeFileList, $sqlFile);
            file_put_contents($sqlFile, "{$scheme};");
            $output->writeln("<info> Reverse:  [CreateFile] {$sqlFile} </info>");
        }

        $schemeFileList = array_filter(array_unique($schemeFileList));
        foreach ($schemeFileList as $sqlFile) {
            $table = pathinfo($sqlFile, PATHINFO_FILENAME);
            if ($schemeOnly && !in_array($table, $schemeOnly)) {
                continue;
            }
            if ($schemeStartOnly && strpos($table, $schemeStartOnly) !== 0) {
                continue;
            }

            $output->write(Abs::ENTER);
            if (!in_array($table, $schemeReverse)) {
                $remove = $params['scheme-prefix-mode'] === 'remove';
                $table = $schemePrefix ? Helper::schemeNamePrefixHandler($table, $schemePrefix, $remove) : $table;
            }

            $exists = $pdo->fetchArray("SHOW TABLES WHERE Tables_in_{$database} = '{$table}'");
            $record = $exists && current($pdo->fetchArray("SELECT COUNT(*) FROM {$table}"));

            if (!$record || $params['scheme-force'] === 'yes') {
                $sql = file_get_contents($sqlFile);
                $sql = str_replace('{TABLE_NAME}', $table, $sql);
                $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
                $pdo->exec($sql);
                $output->writeln("<info>  Scheme:  [ReBuild] {$database}.{$table} </info>");
            } else {
                $output->writeln("<info>  Scheme:  [NotBlank] {$database}.{$table} </info>");
            }

            // Entity & Repository
            if ($scaffoldNeed) {
                $this->web->commandCaller(
                    'bsw:scaffold',
                    [
                        'table'               => $table,
                        'doctrine'            => $params['doctrine'] ?? Abs::DOCTRINE_DEFAULT,
                        'doctrine-is-default' => $params['doctrine-is-default'],
                        'app'                 => $this->app,
                        'cover'               => $params['scaffold-cover'] ?: 'no',
                        'path'                => $params['scaffold-path'] ?: null,
                        'namespace'           => $params['scaffold-ns'] ?: null,
                        'acme'                => $params['acme'],
                        'directory'           => $params['directory'] ?: null,
                        'comment-2-label'     => $params['comment-2-label'] ?: 'no',
                        'comment-2-menu'      => $params['comment-2-menu'] ?: 'no',
                    ],
                    $output
                );
            }
        }

        file_put_contents($doneFile, date(Abs::FMT_FULL));
        $output->writeln("<info>\n Project initialization done\n </info>");
    }
}