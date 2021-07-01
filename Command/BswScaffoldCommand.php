<?php

namespace Leon\BswBundle\Command;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswApiController;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Controller\BswFrontendController;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\CommandException;
use Leon\BswBundle\Module\Exception\FileNotExistsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use InvalidArgumentException;

/**
 * @property ContainerInterface $container
 */
class BswScaffoldCommand extends Command
{
    use BswFoundation;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $acme;

    /**
     * @var string
     */
    protected $cover = 'no';

    /**
     * @var array
     */
    protected $app = ['.all'];

    /**
     * @var string
     */
    protected $doctrine = Abs::DOCTRINE_DEFAULT;

    /**
     * @var bool
     */
    protected $doctrineIsDefault;

    /**
     * @return array
     */
    public function args(): array
    {
        $opt = InputOption::VALUE_OPTIONAL;

        return [
            'table'               => [null, InputOption::VALUE_REQUIRED, 'Table name'],
            'tabled'              => [null, $opt, 'Table real name'],
            'doctrine'            => [null, $opt, 'Doctrine database flag'],
            'doctrine-is-default' => [null, $opt, 'Doctrine is default doctrine', 'yes'],
            'info'                => [null, $opt, 'Module information'],
            'app'                 => [null, $opt, 'App type'],
            'cover'               => [null, $opt, 'Cover file if exists', 'no'],
            'exclude'             => [null, $opt, 'Exclude module split by comma'],
            'args'                => [null, $opt, 'Args string like query string'],
            'path'                => [null, $opt, 'File save path'],
            'namespace'           => [null, $opt, 'Namespace for Controller\Entity\Repository'],
            'directory'           => [null, $opt, 'The directory where is scaffold template'],
            'comment-2-label'     => [null, $opt, 'Fields comment to label', 'no'],
            'comment-2-menu'      => [null, $opt, 'Tables comment to menu', 'no'],
            'acme'                => [
                null,
                $opt,
                'Acme controller class for preview/persistence/filter annotation hint',
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
            'keyword' => 'scaffold',
            'info'    => 'Project scaffold builder',
        ];
    }

    /**
     * Logic
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     * @throws
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->params = $this->options($input);
        extract($this->params);

        /**
         * @var string $table
         * @var string $tabled
         * @var string $doctrine
         * @var string $info
         * @var string $app
         * @var string $cover
         * @var string $exclude
         * @var string $args
         * @var string $path
         * @var string $namespace
         * @var string $directory
         * @var string $acme
         */

        if (empty($table)) {
            throw new InvalidOptionException('Table name is required');
        }

        /**
         * Handler args
         */

        $table = strtolower(Helper::camelToUnder($table));
        parse_str($args, $args);

        // path
        $this->path = rtrim($path ?: $this->kernel->getProjectDir() . '/src', '/');
        if (!file_exists($this->path)) {
            throw new FileNotExistsException("File path not exists `{$this->path}`");
        }

        // cover
        if (is_int($cover) || in_array($cover, ['yes', 'no'])) {
            $this->cover = $cover;
        }

        // app
        if (in_array($app, [Abs::APP_TYPE_API, Abs::APP_TYPE_WEB, Abs::APP_TYPE_FRONTEND, Abs::APP_TYPE_BACKEND])) {
            array_push($this->app, ".{$app}");
            if ($app == Abs::APP_TYPE_API) {
                $this->acme = BswApiController::class;
            } elseif ($app == Abs::APP_TYPE_WEB || $app == Abs::APP_TYPE_FRONTEND) {
                $this->acme = BswFrontendController::class;
            } elseif ($app == Abs::APP_TYPE_BACKEND) {
                $this->acme = BswBackendController::class;
            }
        }

        // namespace
        $this->namespace = $namespace ? trim($namespace, '\\') : 'App';

        // directory of template
        $this->directory = $directory;
        if (empty($this->directory) || !is_dir($this->directory)) {
            $this->directory = __DIR__ . '/scaffold';
        }

        // acme controller
        if (!empty($acme)) {
            $acme = trim($acme, '\\');
            $this->acme = strpos($acme, '\\') ? $acme : "{$this->namespace}\\{$acme}";
            if (!class_exists($this->acme)) {
                throw new InvalidArgumentException("The acme controller {$this->acme} is not found");
            }
        }

        /**
         * Scaffold
         */

        $tree = [];
        Helper::directoryIterator(
            $this->directory,
            $tree,
            function ($file) use ($app) {
                foreach (array_filter([$app, 'all']) as $suffix) {
                    if (Helper::strEndWith($file, $suffix)) {
                        return $file;
                    }
                }

                return false;
            }
        );

        $exclude = Helper::stringToArray($exclude, true, true);

        /**
         * @var $exclude array
         */
        if (!empty($exclude)) {
            foreach ($exclude as $delete) {
                unset($tree[$delete]);
            }
        }
        $tree = Helper::multipleToOne($tree);

        /**
         * Template variables
         */
        if (!empty($doctrine)) {
            $this->doctrine = $doctrine;
        }
        $this->doctrineIsDefault = $this->params['doctrine-is-default'] === 'yes';

        $entityDocument = $this->entityDocument($tabled ?: $table, !!$tabled);
        if (empty($entityDocument)) {
            throw new CommandException("Table `{$table}` in doctrine `{$this->doctrine}` is not found");
        }

        $previewDocument = $this->previewDocument($tabled ?: $table);
        $persistenceDocument = $this->persistenceDocument($tabled ?: $table);

        $entityName = 'Entity';
        $repositoryName = 'Repository';
        $doctrineInDir = Helper::underToCamel($this->doctrine, false);
        if (!$this->doctrineIsDefault) {
            $entityName = "Entity{$doctrineInDir}";
            $repositoryName = "Repository{$doctrineInDir}";
        }

        $variables = array_merge(
            [
                'EntityUse'         => $entityDocument['EntityUse'] ?? null,
                'EntityClass'       => $entityDocument['EntityClass'] ?? null,
                'EntityFields'      => $entityDocument['EntityFields'] ?? null,
                'PreviewTailor'     => $previewDocument['PreviewTailor'] ?? null,
                'PersistenceTailor' => $persistenceDocument['PersistenceTailor'] ?? null,
                'Name'              => Helper::underToCamel($table, false),
                'Namespace'         => $this->namespace,
                'RouteUri'          => str_replace('_', '-', $table),
                'Route'             => $table,
                'Info'              => ucfirst($info ?: str_replace('_', ' ', $table)),
                'AcmeNamespace'     => $this->acme,
                'Acme'              => Helper::clsName($this->acme ?? ''),
                'EntityName'        => $entityName,
                'RepositoryName'    => $repositoryName,
            ],
            $args
        );

        foreach ($tree as $old) {
            $new = $old;
            $content = file_get_contents($old);

            foreach ($variables as $vk => $vv) {
                $new = str_replace("{{$vk}}", $vv, $new);
                $content = str_replace("{{$vk}}", $vv, $content);
                $content = str_replace('\\\\', '\\', $content);
            }

            $info = $this->move($new, $content, $entityName, $repositoryName);
            $output->writeln("<info> Mission: {$info} </info>");
        }

        return $output->writeln("<info>\n Scaffold build done\n </info>");
    }

    /**
     * Move file
     *
     * @param string $file
     * @param string $content
     * @param string $entityName
     * @param string $repositoryName
     *
     * @return string
     */
    private function move(string $file, string $content, string $entityName, string $repositoryName): string
    {
        $file = str_replace($this->directory, $this->path, $file);
        $file = str_replace($this->app, null, $file);

        @mkdir(pathinfo($file, PATHINFO_DIRNAME), 0755, true);
        if (is_int($cover = $this->cover)) {

            $row = substr_count(@file_get_contents($file), "\n");
            $coverAble = ($row >= $this->cover ? 'no' : 'yes');

            $map = [
                '/Controller/' => $coverAble,
                '/Entity/'     => 'yes',
                '/Repository/' => $coverAble,
            ];

            foreach ($map as $keyword => $coverAble) {
                if (strpos($file, $keyword) !== false) {
                    $cover = $coverAble;
                    break;
                }
            }
        }

        $content = preg_replace("/\n([ \n]+)\n/", "\n\n", $content);
        $file = str_replace(
            ['/Controller/', '/Entity/', '/Repository/'],
            ["/Controller/", "/{$entityName}/", "/{$repositoryName}/"],
            $file
        );

        if (!file_exists($file)) {
            file_put_contents($file, $content);
            $tag = ' [Newly]';
        } elseif ($cover == 'yes') {
            file_put_contents($file, $content);
            $tag = ' [Cover]';
        } else {
            $tag = '[Exists]';
        }

        return "{$tag} {$file}";
    }

    /**
     * Hints for tailor
     *
     * @param string $fn
     * @param string $table
     * @param array  $fields
     *
     * @return array
     */
    private function tailorHints(string $fn, string $table, array $fields): array
    {
        if (!$this->acme || !method_exists($this->acme, $fn)) {
            return [];
        }

        $hint = [];
        foreach ($fields as $field => $item) {
            $item = (object)$item;
            $result = call_user_func_array([$this->acme, $fn], [$item, $table, $fields]);
            if (is_array($result)) {
                [$key, $fields] = $result;
                $hint[$key][] = $fields;
            } elseif (is_scalar($result)) {
                $hint[$result][] = Helper::underToCamel($field);
            }
        }

        return $hint;
    }

    /**
     * Array chunk for tailor
     *
     * @param array  $map
     * @param string $table
     * @param array  $fields
     *
     * @return array
     */
    private function tailorArrayChunk(array $map, string $table, array $fields): array
    {
        foreach ($map as $key => $item) {
            $hint = $this->tailorHints($item, $table, $fields);
            $content = null;
            if ($hint) {
                $tpl = str_replace("\n", "\n    ", Helper::printPhpArray($hint));
                $content = file_get_contents("{$this->directory}/Chunk/{$key}.chunk");
                $content = str_replace('{ArrayChunk}', $tpl, $content);
                $content = str_replace("\n", "\n    ", $content);
            }

            $map[$key] = $content;
        }

        return $map;
    }

    /**
     * Get document about preview
     *
     * @param string $table
     *
     * @return array
     * @throws
     */
    private function previewDocument(string $table): array
    {
        $document = $this->web->mysqlSchemeDocument($table, $this->doctrine);
        if (empty($document)) {
            return [];
        }

        if ($this->params['comment-2-menu'] == 'yes') {
            $pdo = $this->pdo($this->doctrine);
            $record = $pdo->fetchOne(
                'SELECT * FROM bsw_admin_menu WHERE `route_name` = ?',
                ["app_{$table}_preview"],
                [Types::STRING]
            );
            $iconMap = [
                'b:icon-creditlevel',
                'b:icon-assessedbadge',
                'b:icon-office',
                'b:icon-similarproduct',
                'b:icon-process',
                'b:icon-electrical',
                'b:icon-app',
                'b:icon-earth',
                'b:icon-icon-74',
                'b:icon-icon-76',
                'b:icon-heartpulse',
                'b:icon-saoyisao',
                'b:icon-smile',
                'b:icon-favorite',
                'b:icon-remind1',
                'b:icon-pin',
                'b:icon-gifts',
                'b:icon-tag',
                'b:icon-icon-63',
                'b:icon-hot',
            ];
            if (!$record) {
                static $times = 0;
                if (strpos($table, 'user') !== false) {
                    $icon = 'b:icon-account';
                } elseif (strpos($table, 'cnf') !== false || strpos($table, 'config') !== false) {
                    $icon = 'b:icon-set';
                } elseif (strpos($table, 'log') !== false || strpos($table, 'history') !== false) {
                    $icon = 'b:icon-history';
                } else {
                    $icon = $iconMap[$times % count($iconMap)];
                    $times += 1;
                }
                $pdo->insert(
                    'bsw_admin_menu',
                    [
                        'route_name' => "app_{$table}_preview",
                        'icon'       => $icon,
                        'value'      => $document['comment'] ?: Helper::stringToLabel($table),
                        'sort'       => 1,
                    ]
                );
            }
        }

        return $this->tailorArrayChunk(
            [
                'PreviewTailor' => Abs::FN_PREVIEW_HINT,
            ],
            $table,
            Helper::arrayColumn($document['fields'], true, 'name')
        );
    }

    /**
     * Get document about persistence
     *
     * @param string $table
     *
     * @return array
     */
    private function persistenceDocument(string $table): array
    {
        $document = $this->web->mysqlSchemeDocument($table, $this->doctrine);
        if (empty($document)) {
            return [];
        }

        return $this->tailorArrayChunk(
            [
                'PersistenceTailor' => Abs::FN_PERSISTENCE_HINT,
            ],
            $table,
            Helper::arrayColumn($document['fields'], true, 'name')
        );
    }

    /**
     * Get document about entity
     *
     * @param string $table
     * @param bool   $tabled
     *
     * @return array
     */
    private function entityDocument(string $table, bool $tabled = false): array
    {
        $document = $this->web->mysqlSchemeDocument($table, $this->doctrine);
        if (empty($document)) {
            return [];
        }

        $fields = $document['fields'];
        $index = $document['index'];

        $EntityUseMap = [
            'foundation' => "use Leon\BswBundle\Entity\FoundationEntity;",
            'orm'        => "use Doctrine\ORM\Mapping as ORM;",
            'unique'     => "use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;",
            'validator'  => "use Symfony\Component\Validator\Constraints as Assert;",
            'annotation' => "use Leon\BswBundle\Annotation\Entity as BswAnnotation;",
            'abs'        => "use Leon\BswBundle\Module\Entity\Abs as BswAbs;",
            'enum'       => "use Leon\BswBundle\Module\Entity\Enum as BswEnum;",
            'hook'       => "use Leon\BswBundle\Module\Hook\Entity as BswHook;",
            'form'       => "use Leon\BswBundle\Module\Form\Entity as BswForm;",
            'filter'     => "use Leon\BswBundle\Module\Filter\Entity as BswFilter;",
            'helper'     => "use Leon\BswBundle\Component\Helper as BswHelper;",
        ];

        $EntityClassMap = [
            'begin'      => "/**",
            'entity'     => " * @ORM\Entity(repositoryClass=\"{Namespace}\{RepositoryName}\{Name}Repository\")",
            'name'       => " * @ORM\Table(name=\"{$table}\")",
            'unique'     => " * @UniqueEntity(fields=%s, errorPath=\"%s\", message=\"Record exists\"%s)",
            'uniques'    => [],
            'property'   => " * @property-read %s %s",
            'properties' => [],
            'end'        => " */",
        ];

        $EntityFieldsMap = [
            'begin'       => "/**",
            'id'          => " * @ORM\Id",
            'auto'        => " * @ORM\GeneratedValue",
            'column'      => " * @ORM\Column(type=\"%s\", name=\"`%s`\")",
            'type'        => " * @Assert\Type(type=\"%s\"%s)",
            'notnull'     => " * @Assert\NotNull(%s)",
            'length'      => " * @Assert\Length(max=%d%s)",
            'preview'     => " * @BswAnnotation\Preview(%s)",
            'persistence' => " * @BswAnnotation\Persistence(%s)",
            'filter'      => " * @BswAnnotation\Filter(%s)",
            'mixed'       => " * @BswAnnotation\Mixed(%s)",
            'end'         => " */",
        ];

        /**
         * @see doctrine: https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#doctrine-mapping-types
         * @see validator: https://symfony.com/doc/current/reference/constraints/Type.html#reference-constraint-type-type
         */
        $dbMapToColumnType = [
            Abs::MYSQL_TINYINT    => ['doctrine' => 'smallint', 'validator' => Abs::T_INTEGER],
            Abs::MYSQL_SMALLINT   => ['doctrine' => 'smallint', 'validator' => Abs::T_INTEGER],
            Abs::MYSQL_MEDIUMINT  => ['doctrine' => 'integer', 'validator' => Abs::T_INTEGER],
            Abs::MYSQL_INT        => ['doctrine' => 'integer', 'validator' => Abs::T_INTEGER],
            Abs::MYSQL_INTEGER    => ['doctrine' => 'integer', 'validator' => Abs::T_INTEGER],
            Abs::MYSQL_BIGINT     => ['doctrine' => 'bigint', 'validator' => Abs::T_NUMERIC],
            Abs::MYSQL_CHAR       => ['doctrine' => 'string', 'validator' => Abs::T_STRING],
            Abs::MYSQL_VARCHAR    => ['doctrine' => 'string', 'validator' => Abs::T_STRING],
            Abs::MYSQL_TINYTEXT   => ['doctrine' => 'text', 'validator' => Abs::T_STRING],
            Abs::MYSQL_TEXT       => ['doctrine' => 'text', 'validator' => Abs::T_STRING],
            Abs::MYSQL_MEDIUMTEXT => ['doctrine' => 'text', 'validator' => Abs::T_STRING],
            Abs::MYSQL_LONGTEXT   => ['doctrine' => 'text', 'validator' => Abs::T_STRING],
            Abs::MYSQL_DATE       => ['doctrine' => 'string', 'validator' => Abs::T_STRING],      // Y-m-d
            Abs::MYSQL_TIME       => ['doctrine' => 'string', 'validator' => Abs::T_STRING],      // H:i:s
            Abs::MYSQL_YEAR       => ['doctrine' => 'integer', 'validator' => Abs::T_INTEGER],    // Y
            Abs::MYSQL_DATETIME   => ['doctrine' => 'string', 'validator' => Abs::T_STRING],      // Y-m-d H:i:s
            Abs::MYSQL_TIMESTAMP  => ['doctrine' => 'string', 'validator' => Abs::T_STRING],      // Y-m-d H:i:s
            Abs::MYSQL_FLOAT      => ['doctrine' => 'float', 'validator' => Abs::T_NUMERIC],
            Abs::MYSQL_DOUBLE     => ['doctrine' => 'float', 'validator' => Abs::T_NUMERIC],
            Abs::MYSQL_DECIMAL    => ['doctrine' => 'float', 'validator' => Abs::T_NUMERIC],
            Abs::MYSQL_JSON       => ['doctrine' => 'string', 'validator' => Abs::T_STRING],
        ];

        if (!$tabled) {
            unset($EntityClassMap['name']);
        }

        foreach ($index as $k => $v) {
            if ($k == 'PRIMARY' || !$v['unique']) {
                unset($index[$k]);
            }
        }

        $unique = false;
        $EntityFields = [];
        $sort = 1;

        foreach ($fields as $item) {

            $item = (object)$item;
            $field = Helper::underToCamel($item->name);

            // begin annotation
            array_push($EntityFields, $EntityFieldsMap['begin']);

            // doctrine > primary key
            if ($item->flag == 'PRI') {
                array_push($EntityFields, $EntityFieldsMap['id']);
            }

            // doctrine > auto increment
            if ($item->extra == 'auto_increment') {
                array_push($EntityFields, $EntityFieldsMap['auto']);
            }

            // doctrine > column
            $columnType = $dbMapToColumnType[$item->type]['doctrine'] ?? 'string';
            $validatorType = $dbMapToColumnType[$item->type]['validator'] ?? 'string';

            array_push($EntityFields, sprintf($EntityFieldsMap['column'], $columnType, $item->name));
            /*
            array_push(
                $EntityClassMap['properties'],
                sprintf($EntityClassMap['property'], $validatorType, "\${$field}")
            );*/

            /**
             * @param bool $comma
             * @param bool $class
             *
             * @return string
             */
            $createGroup = function (bool $comma = true, bool $class = false) use ($item): string {
                $groups = [Abs::VALIDATOR_GROUP_MODIFY];
                if ($class || (!isset($item->default) && $item->flag != 'PRI')) {
                    array_push($groups, Abs::VALIDATOR_GROUP_NEWLY);
                }
                $target = "groups={\"" . implode('", "', $groups) . "\"}";

                return $comma ? ", {$target}" : $target;
            };

            // validator > type
            array_push($EntityFields, sprintf($EntityFieldsMap['type'], $validatorType, $createGroup()));

            // validator > notnull
            if (!$item->null) {
                array_push($EntityFields, sprintf($EntityFieldsMap['notnull'], $createGroup(false)));
            }

            // validator > length
            if ($item->length) {
                array_push($EntityFields, sprintf($EntityFieldsMap['length'], $item->length, $createGroup()));
            }

            // annotation > preview/persistence/filter
            $annotation = [
                'preview'     => Abs::FN_ENTITY_PREVIEW_HINT,
                'persistence' => Abs::FN_ENTITY_PERSISTENCE_HINT,
                'filter'      => Abs::FN_ENTITY_FILTER_HINT,
                'mixed'       => Abs::FN_ENTITY_MIXED_HINT,
            ];

            foreach ($annotation as $key => $fn) {
                if (!$this->acme || !method_exists($this->acme, $fn)) {
                    continue;
                }

                $options = call_user_func_array([$this->acme, $fn], [$item, $table, $fields, $this->params]);
                if (!is_null($options)) {
                    if (isset($options->hook) && is_array($options->hook)) {
                        $options->hook = array_filter($options->hook);
                    }
                    $extraOptions = [];
                    if ($key !== 'mixed') {
                        $extraOptions = ['sort' => $sort];
                    }
                    $options = array_merge($extraOptions, (array)$options);
                    $string = Helper::annotationJsonString($options, false, false);
                    array_push($EntityFields, sprintf($EntityFieldsMap[$key], $string));
                }
            }

            // end annotation
            array_push($EntityFields, $EntityFieldsMap['end']);

            // class property
            $default = null;
            if (isset($item->default) && (!preg_match('/^[A-Z_ ]*$/', $item->default) || empty($item->default))) {
                $default = is_numeric($item->default) ? " = {$item->default}" : " = \"{$item->default}\"";
            }

            array_push($EntityFields, "protected \${$field}{$default};");
            array_push($EntityFields, "");

            // For index
            foreach ($index as $i => $v) {
                if (!in_array($item->name, $v['fields'])) {
                    continue;
                }
                $unique = true;
                $fieldsHanding = [];
                foreach ($v['fields'] as $f) {
                    $fieldsHanding[] = Helper::underToCamel($f);
                }
                if (count($v['fields']) <= 1) {
                    $uniques = '"' . current($fieldsHanding) . '"';
                    $errorPath = current($fieldsHanding);
                } else {
                    $uniques = '{"' . implode('", "', $fieldsHanding) . '"}';
                    $errorPath = implode(',', $fieldsHanding);
                }
                array_push(
                    $EntityClassMap['uniques'],
                    sprintf($EntityClassMap['unique'], $uniques, $errorPath, $createGroup(true, true))
                );
            }

            $sort++;
        }

        array_pop($EntityFields);

        // unique
        unset($EntityClassMap['unique'], $EntityClassMap['property']);
        if (!$unique) {
            unset($EntityUseMap['unique']);
        }

        $EntityClassMap = array_filter(array_unique(Helper::multipleToOne($EntityClassMap)));
        $indent = str_pad('', 4, ' ');

        return [
            'EntityUse'    => implode(Abs::ENTER, $EntityUseMap),
            'EntityClass'  => implode(Abs::ENTER, $EntityClassMap),
            'EntityFields' => implode(Abs::ENTER . $indent, $EntityFields),
        ];
    }
}