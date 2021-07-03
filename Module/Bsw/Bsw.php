<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\AnnotationException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Form\Entity\AutoComplete;
use Leon\BswBundle\Module\Form\Entity\Checkbox;
use Leon\BswBundle\Module\Form\Entity\Mentions;
use Leon\BswBundle\Module\Form\Entity\Radio;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Module\Form\Entity\SelectTree;
use Leon\BswBundle\Module\Form\Entity\Transfer;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Repository\FoundationRepository;

abstract class Bsw
{
    /**
     * @const string
     */
    const ENTITY              = 'Entity';
    const QUERY               = 'Query';
    const ANNOTATION          = 'Annotation';
    const ANNOTATION_ONLY     = 'AnnotationOnly';
    const TAILOR              = 'Tailor';
    const ENUM_EXTRA          = 'EnumExtra';
    const INPUT_ARGS_HANDLER  = 'InputArgsHandler';
    const OUTPUT_ARGS_HANDLER = 'OutputArgsHandler';

    /**
     * @var BswWebController
     */
    protected $web;

    /**
     * @var ArgsInput
     */
    protected $input;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $methodTailorBasic = 'tailor';

    /**
     * @var array
     */
    protected $tailor = [];

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $doctrineName = Abs::DOCTRINE_DEFAULT;

    /**
     * @var FoundationEntity
     */
    protected $entityInstance;

    /**
     * @var FoundationRepository
     */
    protected $repository;

    /**
     * Bsw constructor.
     *
     * @param BswWebController $web
     */
    public function __construct(BswWebController $web)
    {
        $this->web = $web;
    }

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return true;
    }

    /**
     * @return bool|array
     */
    public function inheritExcludeArgs()
    {
        return [
            'clsName',
            'clsNameInIframe',
            'clsNameInMobile',
            'size',
            'sizeInIframe',
            'sizeInMobile',
            'i18nAway',
            'i18nArgs',
            'nextRoute',
        ];
    }

    /**
     * @return string
     */
    abstract public function name(): string;

    /**
     * @return string|null
     * @throws
     */
    public function twig(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function css(): ?array
    {
        return null;
    }

    /**
     * @return array
     */
    public function javascript(): ?array
    {
        return null;
    }

    /**
     * @return ArgsInput
     */
    abstract public function input(): ArgsInput;

    /**
     * @return ArgsOutput
     */
    abstract public function logic(): ArgsOutput;

    /**
     * @param ArgsInput $input
     *
     * @throws
     */
    public function initialization(ArgsInput $input)
    {
        $this->input = $input;

        // class/method name
        $this->class = Helper::underToCamel($this->input->cls, false, '-');
        $this->method = Helper::underToCamel($this->input->fn, true, '-');

        $this->input = $this->caller(
            $this->method(),
            self::INPUT_ARGS_HANDLER,
            [get_class($this->input)],
            $this->input,
            $this->arguments(['input' => $this->input])
        );

        // tailor
        $this->tailor = $this->caller(
            $this->method,
            self::TAILOR,
            Abs::T_ARRAY,
            [],
            $this->arguments($this->input->args)
        );

        // entity
        $entityFlag = self::ENTITY;
        $method = $this->method . $entityFlag;

        $this->entity = $this->input->entity ?? $this->caller(
                $this->method,
                $entityFlag,
                Abs::T_STRING,
                null,
                $this->arguments($this->input->args)
            );

        if (empty($this->entity)) {
            return;
        }

        if (!Helper::extendClass($this->entity, FoundationEntity::class)) {
            throw new ModuleException(
                "Method {$method}():string should be class and extend " . FoundationEntity::class
            );
        }

        // entity instance
        $this->entityInstance = new $this->entity;

        // repository
        if ($autoDoctrine = Helper::parseDoctrineName($this->entity)) {
            $this->doctrineName = $autoDoctrine;
        }
        $this->repository = $this->web->repo($this->entity);
    }

    /**
     * @return string
     */
    protected function method(): string
    {
        return "module" . Helper::underToCamel($this->name(), false);
    }

    /**
     * Set arguments
     *
     * @param array ...$target
     *
     * @return Arguments
     */
    protected function arguments(array ...$target): Arguments
    {
        return (new Arguments())->setMany(array_merge(...$target));
    }

    /**
     * Caller
     *
     * @param string    $prefix
     * @param string    $call
     * @param mixed     $type
     * @param mixed     $default
     * @param Arguments $args
     *
     * @return mixed|null
     * @throws
     */
    protected function caller(string $prefix, string $call, $type = null, $default = null, Arguments $args = null)
    {
        if (!method_exists($this->web, $call = "{$prefix}{$call}")) {
            return $default;
        }

        $args = $args ? [$args] : [];
        $data = call_user_func_array([$this->web, $call], $args) ?? $default;

        if ($type) {
            $type = (array)$type;
            $method = get_class($this->web) . "::{$call}():" . Helper::printArray($type, '[%s]', '', ' | ');
            Helper::callReturnType($data, $type, $method);
        }

        return $data;
    }

    /**
     * Tailor handler
     *
     * @param string    $prefix
     * @param string    $call
     * @param mixed     $type
     * @param Arguments $args
     * @param int       $targetIndex
     *
     * @return mixed
     * @throws
     */
    protected function tailor(
        string $prefix,
        string $call,
        $type = null,
        Arguments $args = null,
        int $targetIndex = null
    ) {

        $argument = $args->target;
        $method = "{$prefix}{$call}";

        if (empty($this->tailor)) {
            return $args->default ?? $argument;
        }

        /**
         * Tailor core
         *
         * @param string $class
         * @param string $method
         * @param mixed  $field
         * @param int    $targetIndex
         *
         * @return array
         */
        $tailorCore = function (string $class, string $method, $field, int $targetIndex = null) use (
            $type,
            &$args,
            $argument
        ) {
            $tailor = new $class($this->web, $field);
            $argsHandling = $args ? [$args] : [];
            $argument = call_user_func_array([$tailor, $method], $argsHandling) ?? $argument;
            if (is_array($argument) && isset($targetIndex)) {
                $argument = $argument[$targetIndex];
            }
            $args->target = $argument;

            if ($type) {
                $type = (array)$type;
                $method = get_class($tailor) . "::{$method}():" . Helper::printArray($type, '[%s]', '');
                Helper::callReturnType($argument, $type, $method);
            }

            return $argument;
        };

        foreach ($this->tailor as $class => $fields) {
            $fn = self::TAILOR;
            // check tailor return keys
            if (!Helper::extendClass($class, Tailor::class)) {
                $tailorClass = Tailor::class;
                throw new ModuleException(
                    "{$this->class}::{$this->method}{$fn}() return must be array with {$tailorClass} key"
                );
            }

            // check tailor return values
            if (!is_array($fields) && ($fields !== true)) {
                throw new ModuleException(
                    "{$this->class}::{$this->method}{$fn}() return must be array with array/true value"
                );
            }

            if (!method_exists($class, $method)) {
                continue;
            }

            if ($fields === true) {
                $argument = $tailorCore($class, $method, true);
                continue;
            }

            $i = 0;
            foreach ($fields as $index => $field) {
                $i += 1;
                // check tailor return fields
                foreach ((array)$field as $f) {
                    if (!property_exists($this->entityInstance, $f)) {
                        throw new ModuleException(
                            "Field `{$f}` don't exists in {$this->entity} when {$this->class}::{$this->method}{$fn}() returned"
                        );
                    }
                }
                $argument = $tailorCore($class, $method, $field, $i > 1 ? $targetIndex : null);
            }
        }

        return $argument;
    }

    /**
     * Enum handler
     *
     * @param array $item
     * @param array $args
     *
     * @return array
     */
    protected function handleForEnumExtra(array $item, array $args = []): array
    {
        if (is_string($item['enumExtra'])) {
            $method = self::ENUM_EXTRA . Helper::underToCamel($item['enumExtra'], false);
            $enum = (array)$item['enum'];

            $arguments = $this->arguments(compact('enum'), $args, $this->input->args);
            $enumExtra = $this->caller('acme', $method, Abs::T_ARRAY, [], $arguments);

            $arguments->set('enumExtra', $enumExtra);
            $enumExtra = $this->caller($this->method, $method, Abs::T_ARRAY, $enumExtra, $arguments);

            if (isset($enumExtra)) {
                $item['enum'] = $enumExtra + $enum;
            }
        }

        if ($item['enum'] && $item['enumHandler']) {
            $item['enum'] = call_user_func_array($item['enumHandler'], [$item['enum']]);
            Helper::callReturnType($item['enum'], Abs::T_ARRAY);
        }

        return $item;
    }

    /**
     * Lang the enum meta
     *
     * @param array $enum
     * @param array $hitKeys
     *
     * @return array
     */
    protected function langTheEnumMeta(array $enum, array $hitKeys = []): array
    {
        $hitKeys = array_merge(['label', 'title', 'text'], $hitKeys);
        foreach ($enum as $key => $item) {
            if (is_array($item)) {
                $enum[$key] = $this->langTheEnumMeta($item);
            } elseif (in_array($key, $hitKeys)) {
                $enum[$key] = $this->web->enumLangSimple($item);
            }
        }

        return $enum;
    }

    /**
     * Handle form with enum
     *
     * @param string $field
     * @param Form   $form
     * @param array  $item
     *
     * @throws
     */
    protected function handleFormWithEnum(string $field, Form $form, array $item)
    {
        $enumProperty = [
            Select::class       => 'options',
            Radio::class        => 'options',
            Checkbox::class     => 'options',
            Mentions::class     => 'options',
            SelectTree::class   => 'treeData',
            AutoComplete::class => 'dataSource',
            Transfer::class     => 'dataSource',
        ];

        if (method_exists($form, 'getEnum') && !$form->getEnum()) {
            $formClass = get_class($form);
            if (!is_array($item['enum'])) {
                $exception = $this->getAnnotationException($field);
                throw new AnnotationException(
                    "{$exception} option `enum` (for `{$enumProperty[$formClass]}`) must configure in {$formClass}"
                );
            }
            $enum = $form->enumHandler($item['enum']);
            $enum = $this->langTheEnumMeta($enum);
            $form->setEnum($enum);
        }
    }

    /**
     * Field hook handler
     *
     * @param string $field
     * @param array  $fieldHook
     * @param array  $hooks
     */
    protected function handleForFieldHook(string $field, array $fieldHook, array &$hooks)
    {
        foreach ($fieldHook as $k => $v) {
            if (is_numeric($k) && class_exists($v)) {
                $hook = $v;
                $hookArgs = [];
            } elseif (class_exists($k) && is_array($v)) {
                $hook = $k;
                $hookArgs = $v;
            } else {
                continue;
            }
            if (isset($hook) && isset($hookArgs)) {
                $hooks[$hook][$field] = $hookArgs;
            }
        }
    }

    /**
     * List entity basic fields
     *
     * @param array $query
     *
     * @return array
     * @throws
     */
    protected function listEntityBasicFields(array $query): array
    {
        $entityList = [];
        if (!$this->entity) {
            return $entityList;
        }

        $entityList[$query['alias']] = $this->entity;
        foreach (($query['join'] ?? []) as $alias => $item) {
            if (is_string($item['entity'])) {
                $entityList[$alias] = $item['entity'];
            } elseif (is_array($item['entity']) && isset($item['entity']['from'])) {
                $entityList[$alias] = $item['entity']['from'];
            }
        }

        return $entityList;
    }

    /**
     * Annotation extra item handler
     *
     * @param string $field
     * @param mixed  $item
     * @param array  $annotationFull
     * @param int    $defaultIndex
     *
     * @return array
     */
    protected function handleForAnnotationExtraItem(
        string $field,
        $item,
        array $annotationFull,
        ?int $defaultIndex = null
    ): array {

        if (is_bool($item)) {
            return [$field, []];
        }

        if (!is_array($item)) {
            return [$field, $item];
        }

        if (isset($item['field'])) {
            [$table, $item['field']] = Helper::getAliasAndFieldFromField($item['field']);
            if (!empty($table)) {
                $item['table'] = $table;
            }
        }

        if (empty($item['table']) || empty($item['field'])) {
            return [$field, $item];
        }

        $tableHandling = Helper::dig($item, 'table');
        $fieldHandling = Helper::dig($item, 'field');

        /**
         * For preview
         */
        if (is_null($defaultIndex)) {
            $itemHandling = $annotationFull[$tableHandling][$fieldHandling] ?? [];
            $item = array_merge($itemHandling, $item);

            return [$field, $item];
        }

        /**
         * For filter
         */
        $indexHandling = Helper::dig($item, 'index') ?? $defaultIndex;
        $indexSplit = Abs::FILTER_INDEX_SPLIT;

        $item['field'] = "{$tableHandling}.{$fieldHandling}";
        $fieldHandling = "{$fieldHandling}{$indexSplit}{$indexHandling}";

        $itemHandling = $annotationFull[$tableHandling][$fieldHandling] ?? [];
        $item = array_merge($itemHandling, $item);

        return ["{$field}{$indexSplit}{$indexHandling}", $item];
    }

    /**
     * Show error
     *
     * @param string $message
     * @param int    $code
     * @param array  $args
     * @param string $route
     *
     * @return ArgsOutput
     */
    public function showError(string $message, int $code = 0, array $args = [], string $route = null): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = (new Message($message))
            ->setClassify(Abs::TAG_CLASSIFY_ERROR)
            ->setRoute($route)
            ->setCode($code)
            ->setArgs($args);

        return $output;
    }

    /**
     * Show success
     *
     * @param string $message
     * @param array  $sets
     * @param array  $args
     * @param string $route
     *
     * @return ArgsOutput
     */
    public function showSuccess(string $message, array $sets = [], array $args = [], ?string $route = ''): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = (new Message($message))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS)
            ->setRoute($route)
            ->setSets($sets)
            ->setArgs($args);

        return $output;
    }

    /**
     * Show message
     *
     * @param Message $message
     *
     * @return ArgsOutput
     */
    public function showMessage(Message $message): ArgsOutput
    {
        $output = new ArgsOutput();
        $output->message = $message;

        return $output;
    }

    /**
     * @return array
     */
    public function entityDocument(): array
    {
        return $this->web->caching(
            function () {
                $table = Helper::tableNameFromCls($this->entity);
                $document = $this->web->mysqlSchemeDocument($table, $this->doctrineName);
                if (empty($document)) {
                    return [];
                }

                return Helper::arrayColumn($document['fields'], true, 'name');
            },
            "bsw-entity-{$this->entity}"
        );
    }

    /**
     * Get input auto
     *
     * @param string $name
     *
     * @return mixed
     * @throws
     */
    public function getInputAuto(string $name)
    {
        $inMobile = "{$name}InMobile";
        if ($this->input->mobile && isset($this->input->{$inMobile})) {
            return $this->input->{$inMobile};
        }

        $inIframe = "{$name}InIframe";
        if ($this->input->iframe && isset($this->input->{$inIframe})) {
            return $this->input->{$inIframe};
        }

        if (!isset($this->input->{$name})) {
            throw new ModuleException("Argument `{$name}` is not defined in " . get_class($this->input));
        }

        return $this->input->{$name};
    }
}
