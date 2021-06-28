<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;
use Leon\BswBundle\Module\Exception\CommandException;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Validator\Entity\In;
use Leon\BswBundle\Module\Validator\Entity\InKey;
use Leon\BswBundle\Module\Validator\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use ReflectionMethod;
use stdClass;

class BswDocumentCommand extends Command
{
    use BswFoundation;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $source = '_source';

    /**
     * @var string
     */
    protected $build = '_build';

    /**
     * @var string
     */
    protected $indent;

    /**
     * @var string
     */
    protected $hostApi;

    /**
     * @var string
     */
    protected $hostAnalog;

    /**
     * @var string
     */
    protected $lang;

    /**
     * @var bool
     */
    protected $jsonStrict;

    /**
     * @var array
     */
    protected $routeStart = [];

    /**
     * @var array
     */
    protected $billError = [];

    /**
     * @var array
     */
    protected $billValidator = [];

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'host-api'    => [null, InputOption::VALUE_OPTIONAL, 'Host for api'],
            'host-analog' => [null, InputOption::VALUE_OPTIONAL, 'Host for analog'],
            'lang'        => [null, InputOption::VALUE_OPTIONAL, 'Language for document', 'cn'],
            'json-strict' => [null, InputOption::VALUE_OPTIONAL, 'Strict json response', 'yes'],
            'route-start' => [null, InputOption::VALUE_OPTIONAL, 'Just route start with collect', 'api'],
            'bill-only'   => [null, InputOption::VALUE_OPTIONAL, 'Bill for `Error` and `Validator` only', 'no'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'document',
            'info'    => 'Auto document with annotation use sphinx',
        ];
    }

    /**
     * init
     *
     * @param array $params
     */
    public function init(array $params)
    {
        $this->indent = Helper::enSpace(1, true);
        $this->path = $this->kernel->getProjectDir() . '/document';
    }

    /**
     * Handle string for document
     *
     * @param string $target
     *
     * @return string
     */
    public function stringForDocument(string $target): string
    {
        $target = preg_replace(
            [
                '/\{\{ (.*?) \}\}/',
                '/([\w]+)/',
            ],
            [
                '$1',
                ' `$1` ',
            ],
            $target
        );

        $target = trim($target, ' ');

        return $target ?: '-';
    }

    /**
     * Logic
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $input->getOptions();
        $this->init($params);

        $this->hostApi = $params['host-api'];
        $this->hostAnalog = $params['host-analog'];
        $this->lang = $params['lang'];
        $this->jsonStrict = $params['json-strict'] === 'yes';
        $this->routeStart = Helper::stringToArray($params['route-start'] ?? '');

        $this->billError = $this->web->apiErrorBill($this->lang);
        $this->billValidator = $this->web->apiValidatorBill($this->lang);

        $this->billError = Helper::sortArray($this->billError, 'code', Abs::SORT_ASC);
        $this->billError = array_values($this->billError);

        if ($params['bill-only'] == 'yes') {
            foreach ($this->billError as $item) {
                $class = ltrim($item['class'], '\\');
                $code = $item['code'];
                $description = $this->stringForDocument($item['description']);
                $output->writeln("<info>| {$class} | {$code} | {$description} |</info>");
            }

            $output->writeln("\n<error> ------ </error>\n");

            foreach ($this->billValidator as $item) {
                $class = ltrim($item['class'], '\\');
                $description = $this->stringForDocument($item['description']);
                $message = $this->stringForDocument($item['message']);
                $output->writeln("<info>| {$class} | {$description} | {$message} |</info>");
            }

            exit(ErrorDebugExit::CODE);
        }

        $route = $this->web->getRouteCollection();
        $this->buildRstFile($route);

        // run sphinx build
        `sphinx-build -b html {$this->path}/ {$this->path}/{$this->build}`;

        return $output->writeln("<info>\n Document use sphinx build done\n </info>");
    }

    /**
     * Build rst file
     *
     * @param array $route
     *
     * @return void
     * @throws
     */
    private function buildRstFile(array $route)
    {
        $n = Abs::ENTER;
        $n2 = Abs::ENTER . Abs::ENTER;

        $index = "{$n}.. include:: ./rst/readme.rst{$n}";

        $list = [];
        foreach ($route as $item) {
            foreach ($this->routeStart as $pos) {
                if (strpos($item['route'], $pos) !== 0) {
                    continue 2;
                }
            }
            $list[md5($item['class'])][] = $item;
        }

        $errorBill = null;
        $errorBill .= ".. list-table::{$n}";
        $errorBill .= "{$this->indent}:widths: 15 35 50{$n}";
        $errorBill .= "{$this->indent}:class: bsw-doc-table-error{$n2}";
        $errorBill .= "{$this->indent}* - Code{$n}";
        $errorBill .= "{$this->indent}  - Tiny{$n}";
        $errorBill .= "{$this->indent}  - Description for logger{$n2}";

        foreach ($this->billError as $item) {
            $errorBill .= "{$this->indent}* - **{$item['code']}**{$n}";
            $errorBill .= "{$this->indent}  - ``{$item['tiny']}``{$n}";
            $errorBill .= "{$this->indent}  - {$item['description']}{$n2}";
        }

        file_put_contents("{$this->path}/rst/api_error.rst", $errorBill);

        /**
         * @var Filesystem $fs
         */
        $fs = new Filesystem();

        $fs->remove(["{$this->path}/{$this->source}", "{$this->path}/{$this->build}"]);
        $fs->mkdir(["{$this->path}/{$this->source}", "{$this->path}/{$this->build}"]);

        $moduleIndex = null;
        $moduleIndex .= "{$this->indent}/rst/api_request{$n}";
        $moduleIndex .= "{$this->indent}/rst/api_response{$n}";

        foreach ($list as $module => $api) {
            $moduleIndex .= "{$this->indent}{$this->source}/{$module}{$n}";
            $this->buildApiRstFile("{$this->path}/{$this->source}/{$module}.rst", $api);
        }

        // index (tocTree)
        $index .= "{$n}.. toctree::{$n}";
        $index .= "{$this->indent}:maxdepth: 2{$n}";
        $index .= "{$this->indent}:numbered:{$n}";
        $index .= "{$this->indent}:caption: PROJECT DOCUMENT{$n2}";
        $index .= $moduleIndex;

        file_put_contents($this->path . '/index.rst', $index);
    }

    /**
     * Build rst file for every controller.
     *
     * @param string $rstFile
     * @param array  $apiList
     *
     * @return void
     * @throws
     */
    private function buildApiRstFile(string $rstFile, array $apiList)
    {
        $page = null;
        $lineTitle = $this->line('"');

        /**
         * @param string $append
         * @param int    $n
         * @param int    $indent
         * @param bool   $return
         *
         * @return string
         */
        $append = function (
            ?string $append = null,
            int $n = 1,
            int $indent = 0,
            bool $return = false
        ) use (&$page): string {
            $rst = ($this->indent($indent) . $append . str_repeat(Abs::ENTER, $n));
            if ($return) {
                return $rst;
            }

            return $page .= $rst;
        };

        /**
         * @param array  $table
         * @param array  $list
         * @param string $clsName
         * @param int    $indent
         */
        $appendTable = function (array $table, array $list, string $clsName, int $indent = 0) use ($append) {

            $append(".. list-table::", 1, $indent);

            $widths = implode(' ', array_values($table));
            $append(":widths: {$widths}", 1, $indent + 1);
            $append(":class: {$clsName}", 2, $indent + 1);

            // table header
            $max = count($table) - 1;
            foreach (array_keys($table) as $index => $title) {
                $flag = $index ? ' ' : '*';
                $n = ($index >= $max) ? 2 : 1;
                $append("{$flag} - {$title}", $n, $indent + 1);
            }

            // table body
            foreach ($list as $item) {
                foreach ($item as $i => $v) {
                    $flag = $i ? ' ' : '*';
                    $n = ($i >= $max) ? 2 : 1;
                    $append("{$flag} - {$v}", $n, $indent + 1);
                }
            }
        };

        $append();
        $classInfo = current($apiList)['desc_cls'];
        $classInfo = $this->lang($classInfo);

        if (empty($classInfo)) {
            throw new CommandException(current($apiList)['class'] . ' has no description');
        }

        $append($classInfo);
        $append($this->line('='), 2);

        $maxOrder = count($apiList) - 1;
        $billValidator = array_column($this->billValidator, 'description', 'rule');
        $billValidator[Abs::VALIDATION_IF_SET] = $this->lang('Validation when not blank');

        $tagsMap = [
            '[AUTH]' => '{AUTH}',
            '[USER]' => '{USER}',
            '[AJAX]' => '{AJAX}',
        ];

        foreach ($apiList as $order => $api) {

            $api['desc_fn'] = $this->lang($api['desc_fn']);

            // method description (title)
            $append($api['desc_fn']);
            $append($this->line('-'), 2);

            $cls = $api['class'];
            $clsName = "\\{$cls}";
            $clsStr = addslashes($cls);

            $docFlag = call_user_func([$clsName, Abs::FN_API_DOC_FLAG]);
            foreach ($docFlag as &$item) {
                $item = $this->lang($item);
            }

            // method path (table)
            $append($this->lang('Basic info'));
            $append($lineTitle, 2);

            // namespace
            $instance = new ReflectionMethod($api['class'], $api['method']);
            $file = $instance->getFileName();
            $line = $instance->getStartLine();

            if (PHP_OS === 'Darwin') {
                $file = "`{$clsStr}::{$api['method']}() <phpstorm://open?file={$file}&line={$line}>`_";
            } else {
                $file = "`{$clsStr}::{$api['method']}() <phpstorm://open?url=file://{$file}&line={$line}>`_";
            }

            $host = $this->hostApi ?: $this->web->host();
            $http = $api['http'] ? implode('|', $api['http']) : 'ANY';
            $appendTable(
                ['Title' => 25, 'Content' => 75],
                [
                    ['Method', "**{$http}**"],
                    ['Route Name', $api['route']],
                    ['Route URL', "{$host}{$api['uri']}"],
                    ['Namespace', $file],
                ],
                'bsw-doc-table-basic'
            );

            // license
            $args = [];
            $license = $api['license'];

            foreach ($tagsMap as $tag => $var) {
                if (strpos($api['desc_fn'], $tag) !== false) {
                    $exists = false;
                    foreach ($license as $lc) {
                        if (strpos($lc, $var) !== false) {
                            $exists = true;
                            break;
                        }
                    }
                    !$exists && array_unshift($license, $var);
                }
            }

            // extra document
            $ajaxRequest = false;
            if (!empty($license)) {
                $licenseList = [];
                $n = count($license) > 1;
                foreach ($license as $lc) {
                    if (strpos($lc, '{AJAX}') !== false) {
                        $ajaxRequest = true;
                    }
                    $prefix = $n ? '- ' : null;
                    $licenseList[] = $prefix . Helper::docVarReplace($lc, $docFlag);
                }

                if ($licenseList = array_filter($licenseList)) {
                    $append($this->lang('Supplementary notes'));
                    $append($lineTitle, 2);
                    $append(".. note::");
                    foreach ($licenseList as $l) {
                        $append($l, 1, 1);
                    }
                    $append();
                }
            }

            // request params (warning or table)
            $append($this->lang('Request params'));
            $append($lineTitle, 2);

            // input
            $input = $this->web->getInputAnnotation($api['class'], $api['method']);

            if (empty($input) && empty($api['param'])) {
                // no input (warning)
                $append(".. note::");
                if (!empty($lr = $api['license-request'])) {
                    $n = count($lr) > 1;
                    foreach ($lr as $lc) {
                        $prefix = $n ? '- ' : null;
                        $append($prefix . Helper::docVarReplace($lc, $docFlag), 2, 1);
                    }
                } else {
                    $append($this->lang('Without request params'), 2, 1);
                }

            } else {

                // input params (table)
                $paramList = [];
                foreach ($api['param'] as $name => $item) {

                    $label = Helper::stringToLabel($item['info'] ?: $name);
                    $label = $this->lang($label, 'fields');
                    $label = Helper::docVarReplace($label, $docFlag);

                    $args[] = [$name, $item['type'], $label, Abs::REQ_GET];
                    if ('GET' == $api['http']) {
                        $http = Abs::DOC_TAG_RIGHT . ' GET';
                    } else {
                        $http = Abs::DOC_TAG_WRONG . ' GET';
                    }
                    $paramList[] = [
                        $name,
                        "**Y**",
                        "**-**",
                        "``-``",
                        $http,
                        $label,
                    ];
                }

                $argsIndent = 0;
                foreach ($input as $item) {

                    $label = $item->trans ? $this->lang($item->label, 'fields') : $item->label;
                    $label = Helper::docVarReplace($label, $docFlag);
                    if ($item->remark) {
                        $label .= " ({$item->remark})";
                    }

                    $rules = [];
                    $enumDocument = null;
                    $args[] = [$item->field, $item->type, $label, $item->method ?: current($api['http'])];

                    foreach ($item->rules as $fn => $params) {

                        $argsString = null;

                        if (in_array($fn, [Abs::VALIDATION_IF_SET])) {
                            $validator = null;
                        } else {

                            /**
                             * @var Validator $validator
                             */
                            if (class_exists($fn)) {
                                $validator = $fn;
                                $fn = Helper::clsName($fn);
                            } else {
                                $validator = Helper::underToCamel($fn, false);
                                $validator = "\\Leon\\BswBundle\\Module\\Validator\\Entity\\{$validator}";
                            }

                            $handler = $item->rulesArgsHandler[$fn] ?? null;
                            $validator = new $validator(null, $params, $this->translator, $this->lang, $handler);
                        }

                        if ($params) {

                            $paramsHandling = $validator->arrayArgs();
                            $classHandling = get_class($validator);

                            if (in_array($classHandling, [In::class])) {
                                $enumDocument .= $this->enumDocument($paramsHandling, false);
                                $argsString = false;
                            } elseif (in_array($classHandling, [InKey::class])) {
                                $enumDocument .= $this->enumDocument($paramsHandling, true);
                                $argsString = false;
                            } else {
                                $argsString = Helper::printArray($paramsHandling, '%s', false, ', ');
                            }
                        }

                        $rules[] = [
                            'fn'        => Helper::camelToUnder($fn),
                            'args'      => $argsString,
                            'validator' => $validator,
                        ];
                    }

                    $required = !empty($rules);

                    if ($rules) {

                        $rulesStr = null;
                        $i = 0;

                        foreach ($rules as $rule) {

                            /**
                             * @var Validator $validator
                             */
                            [$fn, $arg, $validator] = array_values($rule);

                            if (!$validator) {
                                $required = false;
                            } else {
                                $required = ($required && $validator->isRequired());
                            }

                            if ($arg === false) {
                                continue;
                            }

                            $indent = $i ? $argsIndent + 2 : $argsIndent;
                            $indentHandling = $argsIndent + 3;

                            $ruleBill = $billValidator[$fn];
                            if (!isset($arg)) {
                                $ruleHanding = $fn;
                            } else {
                                $ruleHanding = "{$fn}({$arg})";
                            }

                            $rulesStr .= $append(".. div:: show-tips", 2, $indent, true);
                            $rulesStr .= $append($ruleHanding, 1, $indentHandling, true);
                            $rulesStr .= $append(".. div:: show-tips-hidden", 2, $indentHandling - 1, true);
                            $rulesStr .= $append(rawurlencode($ruleBill), 1, $indentHandling, true);

                            $i++;
                        }

                    } else {
                        $rulesStr = "``-``";
                    }

                    $item->method = $item->method ?: implode('|', $api['http']);

                    $required = ($required ? 'Y' : 'N');
                    $signature = ($item->sign === Abs::AUTO ? 'AUTO' : ($item->sign ? 'Y' : 'N'));
                    $http = $item->method ?: Abs::REQ_ALL;

                    if (in_array($http, $api['http'])) {
                        $http = Abs::DOC_TAG_RIGHT . " {$http}";
                    } else {
                        $http = Abs::DOC_TAG_WRONG . " {$http}";
                    }

                    $paramList[] = [
                        $item->field,
                        "**{$required}**",
                        "**{$signature}**",
                        $rulesStr,
                        $http,
                        $label . ' ' . $enumDocument,
                    ];
                }

                $appendTable(
                    [
                        'Name'        => 20,
                        'Required'    => 10,
                        'Signature'   => 10,
                        'Validator'   => 20,
                        'Method'      => 15,
                        'Description' => 25,
                    ],
                    $paramList,
                    'bsw-doc-table-request',
                    $argsIndent
                );
            }

            // analog request
            $append($this->lang('Analog question'));
            $append($lineTitle, 2);

            if (empty($this->hostAnalog)) {
                $append($this->lang('Should configure analog host'), 2);
            } else {
                $host = $this->hostApi ?: $this->web->host();
                $analog = $host . str_replace('.{_format}', null, $api['uri']);
                if (strpos($analog, 'http') === false) {
                    $analog = "http:{$analog}";
                }
                $analog = [
                    'method' => current($api['http']),
                    'api'    => $analog,
                    'args'   => $args,
                ];
                $analog = $this->hostAnalog . '/api?s=' . Helper::jsonStringify64($analog);
                $debug = $this->lang('Analog debug interface');
                $append("`{$debug}  ‹{$api['desc_fn']}› <{$analog}>`_", 2);
            }

            // response params (warning or table)
            $setsType = strtolower($api['tutorial'] ?? 'object');
            $responseParams = $this->lang('Response params');
            $append("{$responseParams} ``({$setsType})``");
            $append($lineTitle, 2);

            // output
            $property = $this->web->getOutputAnnotation($api['class'], $api['method']);

            if (!empty($lr = $api['license-response'])) {
                $append(".. note::");
                $n = count($lr) > 1;
                foreach ($lr as $lc) {
                    $prefix = $n ? '- ' : null;
                    $append($prefix . Helper::docVarReplace($lc, $docFlag), 2, 1);
                }
            }

            // output params (table)
            $propertyHanding = [];
            foreach ($property as $name => $item) {
                $item['indent'] = Helper::cnSpace($item['tab']) . ($item['tab'] ? Abs::DOC_TAG_TREE : null);
                $item['field'] = current(array_reverse(explode('.', $name)));
                $item['label'] = $item['label'] ?? $item['field'];
                $propertyHanding[$name] = $item;
            }

            $propertyIndent = 0;
            $propertyList = [];

            foreach ($property = $propertyHanding as $name => $item) {

                if ($item['field'] === Abs::DOC_KEY_LINE) {
                    $item['field'] = $item['type'] = Abs::DOC_TAG_LINE;
                    $item['label'] = "**{$item['label']}**";
                    $item['trans'] = false;
                }

                $label = Helper::stringToLabel($item['label']);
                $label = $item['trans'] ? $this->lang($label, 'fields') : $label;
                $label = Helper::docVarReplace($label, $docFlag);

                $enumDocument = $this->enumDocument($item['enum']);
                $indent = $this->indent($propertyIndent + 3);
                $type = $item['type'];
                $propertyList[] = [
                    "{$item['indent']}{$item['field']}",
                    strpos($type, Abs::DOC_TAG_LINE) === 0 ? $type : ".. div:: show-tips\n\n{$indent}{$type}",
                    "{$item['indent']}{$label} {$enumDocument}",
                ];
            }

            if ($property) {
                $appendTable(
                    ['Name' => 30, 'Type' => 20, 'Description' => 50],
                    $propertyList,
                    'bsw-doc-table-response',
                    $propertyIndent
                );
            } else {
                $append(".. note::");
                $append($this->lang('Without response params in sets'), 2, 1);
            }

            // response params demo
            $append($this->lang('Response params demo'));
            $append($lineTitle, 2);
            if ($this->jsonStrict) {
                $append(".. code-block:: json", 2);
            } else {
                $append(".. code-block::", 2);
            }

            $setsTypeMap = [
                'array'    => ['begin' => '[', 'end' => ']'],
                'array[]'  => ['begin' => '[[', 'end' => ']]'],
                'object[]' => ['begin' => '[{', 'end' => '}]'],
                'object'   => ['begin' => '{', 'end' => '}'],
            ];

            if (!isset($setsTypeMap[$setsType])) {
                throw new CommandException(
                    "`{$setsType}` is invalid type, must in " . implode('、', array_keys($setsTypeMap))
                );
            }

            if ($property) {
                $setsEnd = $setsTypeMap[$setsType]['end'];
                $setsEndHalf = null;
            } else {
                $setsEnd = null;
                $setsEndHalf = $setsTypeMap[$setsType]['end'];
            }

            $docString = null;
            $docKeys = [];
            $docKeysArgs = [];
            $maxKeyLen = $maxDemoLen = 0;
            $fn = ($ajaxRequest ? Abs::FN_RESPONSE_KEYS_AJAX : Abs::FN_RESPONSE_KEYS);

            if (method_exists($clsName, $fn)) {

                $docKeys = call_user_func([$clsName, $fn]);
                $docKeysArgs = Helper::dig($docKeys, 'args');

                $maxKeyLen = max(Helper::arrayLength($docKeys, true, 'key')) + 3;
                $maxDemoLen = max(Helper::arrayLength($docKeys, true, 'demo')) + 3;
                $docKeys['data']['demo'] = $setsTypeMap[$setsType]['begin'];
            }

            if ($this->jsonStrict) {

                $json = [];
                $dataKey = Abs::UNKNOWN;

                foreach ($docKeys as $k => $item) {
                    if ($k == 'data') {
                        $dataKey = $item['key'];
                        $json[$dataKey] = [];
                    } elseif ($k == 'message') {
                        $json[$item['key']] = "Message by logic";
                    } else {
                        $json[$item['key']] = $item['demo'];
                    }
                }

                foreach ($property as $k => $item) {
                    $json[$dataKey][$k] = $item['type'];
                }

                // handle the sets
                $correctMap = [];
                $correctList = [];

                foreach ($setsTypeMap as $key => $value) {
                    if (strpos($key, 'array') !== false || strpos($key, '[]') !== false) {
                        $correctMap[$key] = [];
                    } else {
                        $correctMap[$key] = new stdClass();
                    }
                }

                foreach ($json[$dataKey] ?? [] as $key => $value) {
                    if (isset($correctMap[$value])) {
                        $correctList[] = $key;
                    }
                }

                $correctSets = [];
                foreach ($json[$dataKey] ?? [] as $key => $value) {
                    $keys = explode('.', $key);
                    $first = current($keys);
                    if ((count($keys) > 1) && in_array($first, $correctList)) {
                        $key = preg_replace("/{$first}\.(\w+)/", "{$first}.0.$1", $key);
                    }
                    $correctSets[$key] = $correctMap[$value] ?? $value;
                }

                $json[$dataKey] = Helper::oneDimension2n($correctSets);
                $json[$dataKey] = Helper::recursionValueHandler(
                    $json[$dataKey],
                    function ($item, $key) {
                        if ($item == Abs::T_INT || $item == Abs::T_INTEGER) {
                            $item = rand(0, 32);
                        } elseif ($item == Abs::T_FLOAT || $item == Abs::T_DOUBLE) {
                            $item = rand(0, 32 * 100) / 100;
                        } elseif ($item == Abs::T_STRING) {
                            $item = $this->lang(Helper::stringToLabel($key), 'fields');
                        } elseif ($item === Abs::T_BOOL) {
                            $item = (bool)rand(0, 1);
                        } else {
                            $item = new stdClass();
                        }

                        return $item;
                    }
                );

                if ($setsType == 'object[]' || $setsType == 'array[]') {
                    $json[$dataKey] = [$json[$dataKey]];
                }

                if ($setsType == 'object' && empty($json[$dataKey])) {
                    $json[$dataKey] = new stdClass();
                }

                $append(Helper::formatPrintJson($json, 2, ': ', $this->indent()));

            } else {

                foreach ($docKeys as $k => $item) {
                    if (empty($item)) {
                        continue;
                    }
                    $key = $this->pad("\"{$item['key']}\"", $maxKeyLen);
                    $demo = ($k == 'data') ? "{$item['demo']}" : "{$item['demo']},";
                    if ($setsEndHalf && $k == 'data') {
                        $demo .= $setsEndHalf;
                    }
                    $demo = $this->pad($demo, $maxDemoLen);
                    $notes = $this->lang($item['notes'], 'twig', $docKeysArgs);
                    $docString .= $this->indent(2) . "{$key}: {$demo} // {$notes}" . Abs::ENTER;
                }

                $append($setsTypeMap['object']['begin'], 1, 1);
                $append($docString, 0);

                if ($property) {
                    $maxKeyLen = max(Helper::arrayLength($property)) + 3;
                    foreach ($property as $field => $item) {
                        $field = $this->pad("\"{$item['field']}\"", $maxKeyLen);
                        $append("{$item['indent']}{$field}: ({$item['type']})", 1, $docKeys ? 3 : 2);
                    }
                }

                if ($docKeys && $setsEnd) {
                    $append($setsEnd, 1, 2);
                }

                $append($setsTypeMap['object']['end'], 2, 1);
            }

            if ($order != $maxOrder) {
                $append(".. image:: ../_static/img/line.png", 2);
                //$append("----", 2);
            }
        }

        file_put_contents($rstFile, $page);
    }

    /**
     * Document for enum
     *
     * @param array $enum
     * @param bool  $showKey
     *
     * @return string
     */
    private function enumDocument(array $enum, bool $showKey = true): ?string
    {
        $document = null;
        foreach ($enum as $key => $val) {
            $val = $showKey ? $this->lang($val, 'enum') : $val;
            $key = $showKey ? "{$key}:" : null;
            $document .= "``{$key}{$val}`` ";
        }

        return $document;
    }

    /**
     * Translator
     *
     * @param string $key
     * @param string $domain
     * @param array  $args
     *
     * @return string
     */
    private function lang(string $key, string $domain = 'twig', array $args = []): string
    {
        return $this->translator->trans($key, $args, $domain, $this->lang);
    }

    /**
     * Get line
     *
     * @param string $char
     *
     * @return string
     */
    protected function line(string $char): string
    {
        return str_repeat($char, 50);
    }

    /**
     * Indent with 4 space
     *
     * @param int $repeat
     *
     * @return string
     */
    protected function indent(int $repeat = 1): string
    {
        return str_repeat($this->indent, $repeat);
    }

    /**
     * Pad space to right
     *
     * @param string $target
     * @param int    $length
     *
     * @return string
     */
    protected function pad(string $target, int $length)
    {
        return str_pad($target, $length, ' ', STR_PAD_RIGHT);
    }
}