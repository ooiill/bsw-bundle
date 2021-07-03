<?php

namespace Leon\BswBundle\Module\Bsw\Persistence;

use Leon\BswBundle\Annotation\Entity\Persistence;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Entity\ErrorRequestOften;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Exception\LogicException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Module\Form\Entity\Group;
use Leon\BswBundle\Module\Form\Entity\Transfer;
use Symfony\Component\Validator\Exception\ValidatorException;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Form\Entity\CkEditor;
use Leon\BswBundle\Module\Form\Entity\Datetime;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Module\Form\Entity\Upload;
use Leon\BswBundle\Module\Form\Entity\Input as FormInput;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Hook\Entity\JsonStringify;
use Leon\BswBundle\Component\Upload as Uploader;
use Exception;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const BEFORE_HOOK        = 'BeforeHook';
    const AFTER_HOOK         = 'AfterHook';
    const BEFORE_RENDER      = 'BeforeRender';
    const FORM_OPERATE       = 'FormOperates';
    const AFTER_SUBMIT       = 'AfterSubmit';
    const BEFORE_PERSISTENCE = 'BeforePersistence';
    const AFTER_PERSISTENCE  = 'AfterPersistence';
    const CUSTOM_HANDLER     = 'CustomHandler';

    /**
     * @var string
     */
    protected $methodTailor = 'tailorPersistence';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'persistence';
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * Exception about annotation
     *
     * @param string $field
     *
     * @return string
     */
    protected function getAnnotationException(string $field): string
    {
        if ($this->entity) {
            return "@Persistence() in {$this->entity}::{$field}";
        }

        $annotation = self::ANNOTATION;

        return "Item in {$this->method}{$annotation}():array {$field}";
    }

    /**
     * Annotation handler
     *
     * @param array $record
     *
     * @return array|ArgsOutput
     * @throws
     */
    protected function handleAnnotation(array $record)
    {
        /**
         * persistence annotation
         */

        $persistAnnotation = [];
        if ($this->entity) {
            $persistAnnotation = $this->web->getPersistenceAnnotation(
                $this->entity,
                [
                    'enumClass'          => $this->input->enum,
                    'doctrinePrefix'     => $this->web->parameter('doctrine_prefix'),
                    'doctrinePrefixMode' => $this->web->parameter('doctrine_prefix_mode'),
                ]
            );
        }

        /**
         * persistence annotation only
         */

        $fn = self::ANNOTATION_ONLY;
        $arguments = $this->arguments(
            [
                'id'                => $this->input->id,
                'persistence'       => !!$this->input->submit,
                'persistAnnotation' => $persistAnnotation,
            ],
            $this->input->args
        );
        $arguments->set('record', $record);
        $persistAnnotationExtra = $this->caller(
            $this->method,
            $fn,
            [Message::class, Error::class, Abs::T_ARRAY],
            null,
            $arguments
        );

        if ($persistAnnotationExtra instanceof Error) {
            return $this->showError($persistAnnotationExtra->tiny());
        } elseif ($persistAnnotationExtra instanceof Message) {
            return $this->showMessage($persistAnnotationExtra);
        }

        $arguments = $this->arguments(
            ['target' => $persistAnnotationExtra, 'id' => $this->input->id],
            $this->input->args
        );
        $arguments->setMany(compact('record'));
        $persistAnnotationExtra = $this->tailor($this->methodTailor, $fn, [Abs::T_ARRAY, null], $arguments);

        /**
         * extra annotation handler
         */

        if (!is_null($persistAnnotationExtra)) {

            $persistAnnotationOnlyKey = array_keys($persistAnnotationExtra);
            $persistAnnotation = Helper::arrayPull($persistAnnotation, $persistAnnotationOnlyKey);

        } else {

            /**
             * persistence extra annotation
             */

            $fn = self::ANNOTATION;

            $arguments = $this->arguments(
                [
                    'id'                => $this->input->id,
                    'persistence'       => !!$this->input->submit,
                    'persistAnnotation' => $persistAnnotation,
                ],
                $this->input->args
            );
            $arguments->set('record', $record);
            $persistAnnotationExtra = $this->caller(
                $this->method,
                $fn,
                [Message::class, Error::class, Abs::T_ARRAY],
                [],
                $arguments
            );
            if ($persistAnnotationExtra instanceof Error) {
                return $this->showError($persistAnnotationExtra->tiny());
            } elseif ($persistAnnotationExtra instanceof Message) {
                return $this->showMessage($persistAnnotationExtra);
            }

            $arguments = $this->arguments(
                ['target' => $persistAnnotationExtra, 'id' => $this->input->id],
                $this->input->args
            );
            $arguments->setMany(compact('persistAnnotation', 'record'));
            $persistAnnotationExtra = $this->tailor($this->methodTailor, $fn, Abs::T_ARRAY, $arguments);
        }

        /**
         * annotation handler with extra
         */

        if (!$this->input->submit) {
            $persistAnnotationExtra[Abs::FLAG_WEBSITE_TOKEN] = [
                'label' => 'Website token',
                'sort'  => .001,
                'type'  => FormInput::class,
                'value' => $this->web->createWebsiteToken(),
                'hide'  => true,
            ];
        }

        foreach ($persistAnnotationExtra as $field => $item) {

            $itemHandling = $item;
            if (is_bool($item)) {
                $item = [];
            }

            if (!is_array($item)) {
                throw new ModuleException("Persistence {$this->class}::{$this->method}{$fn}() return must be array[]");
            }

            if ($itemHandling === false) {
                $persistAnnotation[$field]['show'] = false;
            }

            if (isset($persistAnnotation[$field])) {
                $item = array_merge($persistAnnotation[$field], $item);
            }

            $original = $this->web->annotation(Persistence::class, true);
            $original->class = $this->class;
            $original->target = $field;

            $item = $original->converter([new Persistence($item)]);
            $persistAnnotation[$field] = (array)current($item[Persistence::class]);
        }

        $persistAnnotation = Helper::sortArray($persistAnnotation, 'sort');

        /**
         * hooks
         */

        $hooks = [];
        $persistAnnotationHandling = $persistAnnotation;

        foreach ($persistAnnotation as $field => $item) {
            /**
             * @var Form $form
             */
            $form = $item['type'];
            if (!$form->formSceneState(Abs::TAG_PERSISTENCE)) {
                $formClass = get_class($form);
                throw new ModuleException("Form item `{$formClass}` not support in persistence scene");
            }

            $this->handleForFieldHook($field, $item['hook'], $hooks);
            if (!$item['show']) {
                unset($persistAnnotation[$field]);
            }
        }

        return [$persistAnnotation, $persistAnnotationHandling, $hooks];
    }

    /**
     * Get persistence data
     *
     * @return ArgsOutput|array
     * @throws
     */
    protected function getPersistenceData()
    {
        if (empty($this->entity)) {
            return [[], [], [], [], [], []]; // just interlude, basically useless
        }

        $key = "{$this->input->route}:record:before";

        /**
         * @var FoundationEntity $record
         */

        if ($this->input->submit) {

            $record = new $this->entity;
            [$submit, $extraSubmit] = $this->resolveSubmit($this->input->submit);

            $recordBefore = $this->web->session->get($key) ?? [];

            [$recordAdd, $recordDel] = Helper::newDifferenceOldWithAssoc($recordBefore, $submit, false);
            $args = compact('submit', 'extraSubmit', 'recordAdd', 'recordDel', 'recordBefore');
            $arguments = $this->arguments(
                $args,
                [
                    'id'                => $this->input->id,
                    'passwordValidator' => $this->input->passwordValidator,
                ],
                $this->input->args
            );
            $result = $this->caller(
                $this->method,
                self::AFTER_SUBMIT,
                [Message::class, Error::class, Abs::T_ARRAY],
                $args,
                $arguments
            );

            if ($result instanceof Error) {
                return $this->showError($result->tiny());
            } elseif ($result instanceof Message) {
                return $this->showMessage($result);
            } else {
                try {
                    [$submit, $extraSubmit] = array_values($result);
                } catch (Exception $e) {
                    $fn = $this->method . self::AFTER_SUBMIT;
                    throw new ModuleException(
                        "Method return illegal in {$this->class}::{$fn}():array, must return array with index 0 and 1"
                    );
                }
            }

            [$recordAdd, $recordDel] = Helper::newDifferenceOldWithAssoc($recordBefore, $submit, false);
            $args = compact('extraSubmit', 'recordAdd', 'recordDel', 'recordBefore');
            $arguments = $this->arguments(
                ['target' => $submit],
                $args,
                [
                    'id'                => $this->input->id,
                    'default'           => [$submit, $extraSubmit],
                    'passwordValidator' => $this->input->passwordValidator,
                ],
                $this->input->args
            );
            $result = $this->tailor(
                $this->methodTailor,
                self::AFTER_SUBMIT,
                [Message::class, Error::class, Abs::T_ARRAY],
                $arguments,
                0
            );

            if ($result instanceof Error) {
                return $this->showError($result->tiny());
            } elseif ($result instanceof Message) {
                return $this->showMessage($result);
            } else {
                try {
                    [$submit, $extraSubmit] = array_values($result);
                } catch (Exception $e) {
                    $fn = $this->method . self::AFTER_SUBMIT;
                    throw new ModuleException(
                        "Method return illegal in Module\Tailor::{$fn}():array, must return array with index 0 and 1"
                    );
                }
            }

            if (!is_array($extraSubmit)) {
                throw new ModuleException('After submit handler extra should be return array');
            }

            $record->attributes($submit, true);
            $record = Helper::entityToArray($record);

            return [$submit, $record, $extraSubmit, $recordBefore, $recordAdd, $recordDel];
        }

        /**
         * Fetch data
         */

        if ($this->entity && $this->input->id) {
            $record = $this->repository->find($this->input->id);
        } else {
            $record = new $this->entity;
        }

        $record = Helper::entityToArray($record);
        $this->web->session->set($key, $record);

        return [[], $record, [], $record, $record, []];
    }

    /**
     * Configure form for frontend
     *
     * @param Form   $form
     * @param string $field
     * @param array  $item
     * @param Output $output
     */
    protected function formConfigureForFrontend(string $field, Form $form, array $item, Output $output)
    {
        if ($form instanceof Upload) {
            if (!$form->getRoute()) {
                $form->setRoute($this->input->cnf->route_upload);
            }

            $form->setUrl($this->web->urlSafe($form->getRoute(), $form->getArgs(), 'Build upload route'));

            /**
             * File list key
             */
            $key = 'persistenceFileListKeyCollect';
            $form->setFileListKey("{$key}.{$field}.list");
            if (!$this->web->routeIsAccess($form->getRouteForAccess())) {
                $form->setDisplay(false);
            }
            $output->fileListKeyCollect[$field] = [
                'key'  => $key,
                'list' => [],
                'id'   => $field,
                'md5'  => $form->getFileMd5Key(),
                'sha1' => $form->getFileSha1Key(),
                'url'  => $form->getFileUrlKey(),
            ];

            /**
             * Upload tips
             */
            $option = $this->web->uploadOptionByFlag($form->getFlag());
            [$list, $suffix, $mime] = Uploader::optionTips(
                $option,
                function ($label) {
                    return $this->web->twigLang($label);
                }
            );

            $output->uploadTipsCollect[$field] = [
                'columns' => [
                    [
                        'title'     => $this->web->fieldLang('Type'),
                        'dataIndex' => 'type',
                        'align'     => Abs::POS_RIGHT,
                        'width'     => 100,
                    ],
                    [
                        'title'     => $this->web->fieldLang('Condition'),
                        'dataIndex' => 'condition',
                        'width'     => 240,
                    ],
                ],
                'list'    => $list,
            ];

            /**
             * Accept
             */
            $accept = null;
            if ($suffix != '*' && strpos($suffix, '!') !== 0) {
                $accept .= ',.' . str_replace('、', ',.', $suffix);
            }
            if ($mime != '*' && strpos($mime, '!') !== 0) {
                $accept .= ',' . str_replace('、', ',', $mime);
            }

            if ($accept) {
                $form->setAccept(ltrim($accept, ','));
            }
        }

        if ($form instanceof Transfer) {
            $key = 'persistenceTransferKeysCollect';
            if (!$form->getChange()) {
                $form->setChange('persistenceTransferChange');
            }

            $form->setTargetKeysKey("{$key}.{$field}.target");
            $form->setSelectedKeysKey("{$key}.{$field}.selected");

            if (isset($item['value'])) {
                $form->setTargetKeys((array)$item['value']);
            }
            $output->transferKeysCollect[$field] = [
                'target'   => $form->getTargetKeysArray(),
                'selected' => $form->getSelectedKeysArray(),
            ];
        }

        if ($vnMeta = $form->getVarNameForMeta()) {
            if (method_exists($form, 'getEnum')) {
                $output->varNameForMetaCollect[$field] = $form->getEnum() ?: $form->getVarNameForMetaDefaultArray();
                $form->setEnum([]);
            } else {
                $output->varNameForMetaCollect[$field] = $form->getValueShadow(); // just use value shadow when not enum
            }
            if ($vnMeta === true) {
                $vnMeta = $field;
            }
            $form->setVarNameForMeta("persistenceVarNameForMetaCollect.{$vnMeta}");
        }

        if ($vnMetaField = $form->getVarNameForMetaField()) {
            $form->setVarNameForMetaField("persistenceVarNameForMetaCollect.{$vnMetaField}");
        }

        if ($fieldHideMeta = $form->getChangeTriggerHide()) {
            $output->fieldHideCollect[$field] = $fieldHideMeta;
        }

        if ($fieldDisabledMeta = $form->getChangeTriggerDisabled()) {
            $output->fieldDisabledCollect[$field] = $fieldDisabledMeta;
        }

        if ($form instanceof Group) {
            foreach ($form->getMember() as $f) {
                $this->formConfigureForFrontend($f->getKey(), $f, $item, $output);
            }
        }
    }

    /**
     * Get datetime format
     *
     * @param string $field
     * @param Form   $form
     * @param array  $format
     */
    protected function datetimeFormat(string $field, Form $form, array &$format = [])
    {
        if (Helper::extendClass($form, Datetime::class, true)) {

            /**
             * @var Datetime $form
             */
            $format[$field] = $form->getFormat();
        }

        if (Helper::extendClass($form, Group::class, true)) {
            /**
             * @var Group $form
             */
            foreach ($form->getMember() as $key => $groupForm) {
                $this->datetimeFormat($groupForm->getKey(), $groupForm, $format);
            }
        }
    }

    /**
     * Persistence data handler
     *
     * @param array  $persistAnnotation
     * @param array  $record
     * @param array  $hooks
     * @param Output $output
     *
     * @return array|ArgsOutput
     * @throws
     */
    protected function handlePersistenceData(array $persistAnnotation, array $record, array $hooks, Output $output)
    {
        /**
         * before hook (row record)
         *
         * @param array $original
         * @param array $extraArgs
         *
         * @return mixed
         */
        $before = function (array $original, array $extraArgs) {
            $arguments = $this->arguments(compact('original', 'extraArgs'), $this->input->args);
            $original = $this->caller($this->method, self::BEFORE_HOOK, Abs::T_ARRAY, $original, $arguments);

            return $this->tailor(
                $this->methodTailor,
                self::BEFORE_HOOK,
                Abs::T_ARRAY,
                $arguments->unset('original')->set('target', $original)
            );
        };

        /**
         * after hook (row record)
         *
         * @param array $hooked
         * @param array $original
         * @param array $extraArgs
         *
         * @return mixed
         */
        $after = function (array $hooked, array $original, array $extraArgs) {
            $arguments = $this->arguments(compact('hooked', 'original', 'extraArgs'), $this->input->args);
            $hooked = $this->caller($this->method, self::AFTER_HOOK, Abs::T_ARRAY, $hooked, $arguments);

            return $this->tailor(
                $this->methodTailor,
                self::AFTER_HOOK,
                Abs::T_ARRAY,
                $arguments->unset('hooked')->set('target', $hooked)
            );
        };

        $persistence = !!$this->input->submit;

        $original = $record;
        $extraArgs = [Abs::HOOKER_FLAG_ACME => ['scene' => $this->input->id ? Abs::TAG_PERSIST_MODIFY : Abs::TAG_PERSIST_NEWLY]];
        $record = $this->web->hooker($hooks, $record, $persistence, $before, $after, $extraArgs);
        $hooked = $record;

        if ($persistence) {
            return [$record, [], [], $original];  // just interlude, basically useless
        }

        /**
         * before render (all record)
         */

        $args = array_merge(
            compact('hooked', 'original', 'persistence'),
            ['id' => $this->input->id],
            $this->input->args
        );

        $arguments = $this->arguments($args);
        $record = $this->caller(
            $this->method,
            self::BEFORE_RENDER,
            [Message::class, Error::class, Abs::T_ARRAY],
            $hooked,
            $arguments
        );
        if ($record instanceof Error) {
            return $this->showError($record->tiny());
        } elseif ($record instanceof Message) {
            return $this->showMessage($record);
        }

        $arguments->set('target', $record);
        $record = $this->tailor(
            $this->methodTailor,
            self::BEFORE_RENDER,
            [Message::class, Error::class, Abs::T_ARRAY],
            $arguments
        );
        if ($record instanceof Error) {
            return $this->showError($record->tiny());
        } elseif ($record instanceof Message) {
            return $this->showMessage($record);
        }

        $recordHandling = [];
        $format = [];
        $view = $this->web->getArgs($this->input->view);

        foreach ($persistAnnotation as $key => $item) {
            /**
             * @var Form $form
             */
            $form = $item['type'];

            $form->setKey($key);
            $form->setLabel($item['label']);
            $form->setStyle($item['style']);
            $form->setField(Helper::camelToUnder($key));

            $form->setDisabled($view ? true : $item['disabled']);
            $form->setDisabledOverall($view ? true : $item['disabledOverall']);
            $form->setFormRules($item['formRules']);
            $form = $this->web->formRulesHandler($form);

            if (isset($record[$key])) {
                $form->setValue($record[$key]);
            }

            if (isset($item['value'])) {
                $v = $item['value'];
                if (is_callable($v)) {
                    $form->setValue(call_user_func_array($v, [$recordHandling, $record, $original]));
                } else {
                    $form->setValue($v);
                }
            }

            if (isset($item['valueShadow'])) {
                $v = $item['valueShadow'];
                if (is_callable($v)) {
                    $form->setValueShadow(call_user_func_array($v, [$recordHandling, $record, $original]));
                } else {
                    $form->setValueShadow($v);
                }
            }

            if (method_exists($form, 'setSize') && method_exists($form, 'isSizeManual') && !$form->isSizeManual()) {
                $form->setSize($this->getInputAuto('size'));
            }

            if (Helper::extendClass($form, Group::class, true)) {
                /**
                 * @var Group $form
                 */
                foreach ($form->getMember() as $f) {
                    if (method_exists($f, 'setSize') && method_exists($f, 'isSizeManual') && !$f->isSizeManual()) {
                        $f->setSize($this->getInputAuto('size'));
                    }
                }
            }

            /**
             * extra enum
             */

            $item = $this->handleForEnumExtra($item, ['scene' => Abs::TAG_PERSISTENCE, 'id' => $this->input->id]);
            $this->handleFormWithEnum($key, $form, $item);
            $this->datetimeFormat($key, $form, $format);

            if (!$form->getPlaceholder()) {
                $form->setPlaceholder($item['placeholder'] ?: $form->getLabel());
            }

            $tipsAuto = $titleAuto = null;
            if (($form instanceof Select) && $form->getMode() == Abs::MODE_MULTIPLE) {
                if (is_null($form->getValue())) {
                    $form->setValue([]);
                }
                if (!$this->input->id || $form->isValueMultiple()) {
                    $tipsAuto = $this->web->twigLang('For multiple operate');
                } else {
                    $form->setMode(Abs::MODE_DEFAULT);
                }
            }

            $this->formConfigureForFrontend($key, $form, $item, $output);
            if ($form->isDynamicRow() && ($form instanceof Group)) {
                $form->setColumnSingle(count($form->getMember()), 2)->pushMember(
                    (new Button())
                        ->setSize($this->getInputAuto('size'))
                        ->setType(Abs::THEME_ELE_WARNING_OL)
                        ->setShape(Abs::SHAPE_CIRCLE)
                        ->setRootClickForVue("((e) => {bsw.cnf.v.{$form->getDynamicRowSub()}(row)})")
                        ->setKey('delete')
                        ->setIcon('a:delete')
                );
            }

            if (in_array(JsonStringify::class, $item['hook'])) {
                $button = (new Button('Verify JSON format'))
                    ->setIcon($this->input->cnf->icon_badge)
                    ->setType(Abs::THEME_LINK)
                    ->setSize(Abs::SIZE_SMALL)
                    ->setClick('verifyJsonFormat')
                    ->setArgs(
                        [
                            'field' => $key,
                            'url'   => $this->input->cnf->verify_json_url,
                            'key'   => $this->input->cnf->verify_json_key,
                        ]
                    );
                $titleAuto = $this->web->getButtonHtml($button, true);
            }

            $recordHandling[$key] = [
                'hide'      => $item['hide'],
                'label'     => $item['trans'] ? $this->web->fieldLang($form->getLabel()) : $form->getLabel(),
                'tips'      => $item['tips'],
                'tipsAuto'  => $tipsAuto,
                'title'     => $item['title'],
                'titleAuto' => $titleAuto,
                'column'    => $item['column'],
                'type'      => $form,
            ];
        }

        $submit = new Button('Submit', $this->input->route, $this->input->cnf->icon_sure);
        $submit->setArgs(['id' => $this->input->id]);
        $submit->setAttributes(['bsw-method' => 'submit']);

        $arguments = $this->arguments(compact('submit', 'record', 'hooked', 'original'), $this->input->args);
        $arguments->set('id', $this->input->id);
        $operates = $this->caller($this->method, self::FORM_OPERATE, Abs::T_ARRAY, [], $arguments);
        $operates = array_merge(['submit' => $submit], $operates);
        $operates = array_filter($operates);

        foreach ($operates as $operate) {

            $buttonCls = Button::class;
            if (!Helper::extendClass($operate, $buttonCls, true)) {
                $fn = self::FORM_OPERATE;
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
            }

            /**
             * @var Button $operate
             */

            $operate->setClick('setUrlToForm');
            $operate->setScript(Html::scriptBuilder($operate->getClick(), $operate->getArgs()));
            $operate->setUrl(
                $this->web->urlSafe($operate->getRoute(), $operate->getArgs(), 'Build persistence operates')
            );

            if ($this->getInputAuto('operatesBlock')) {
                $operate->setBlock(true);
            }

            $operate->setHtmlType(Abs::TYPE_SUBMIT);
            if (!$operate->isSizeManual()) {
                $operate->setSize($this->getInputAuto('size'));
            }
            if (!$this->web->routeIsAccess($operate->getRouteForAccess())) {
                $operate->setDisplay(false);
            }
        }

        return [$recordHandling, $operates, $format, $original];
    }

    /**
     * Resolve submit
     *
     * @param array $submit
     *
     * @return array
     */
    protected function resolveSubmit(array $submit): array
    {
        $extraSubmit = [];
        $document = $this->entityDocument();
        foreach ($submit as $field => $value) {
            $fieldHandling = Helper::camelToUnder($field);
            $fieldHandlingMixed = Helper::mixedToUnder($fieldHandling);
            if (!isset($document[$fieldHandling]) && !isset($document[$fieldHandlingMixed])) {
                $extraSubmit[$field] = $value;
                unset($submit[$field]);
                continue;
            }
        }

        return [$submit, $extraSubmit];
    }

    /**
     * Record handler
     *
     * @param array  $submit
     * @param array  $record
     * @param array  $persistAnnotationHandling
     * @param array  $extraSubmit
     * @param string $multipleField
     *
     * @return array
     */
    protected function recordHandler(
        array $submit,
        array $record,
        array $persistAnnotationHandling,
        array $extraSubmit,
        string $multipleField = null
    ): array {

        $document = $this->entityDocument();
        foreach ($record as $field => $value) {

            $fieldHandling = Helper::camelToUnder($field);

            // Field don't exists
            if (!isset($persistAnnotationHandling[$field])) {
                unset($record[$field]);
                continue;
            }

            // Force ignore
            if ($persistAnnotationHandling[$field]['ignore']) {
                unset($record[$field]);
                continue;
            }

            // No show and not in submit
            if (!$persistAnnotationHandling[$field]['show'] && !isset($submit[$field])) {
                unset($record[$field]);
                continue;
            }

            // When field is null but default is not null
            if (is_null($value) && $document[$fieldHandling]['default'] !== null) {
                unset($record[$field]);
                continue;
            }

            // Ignore when blank
            if ($persistAnnotationHandling[$field]['ignoreBlank'] && trim($value) === '') {
                unset($record[$field]);
                continue;
            }
        }

        foreach ($extraSubmit as $field => $value) {

            $fieldHandling = Helper::camelToUnder($field);

            // Field not in annotation
            if (!isset($persistAnnotationHandling[$field])) {
                unset($extraSubmit[$field]);
                continue;
            }

            // Field not in entity
            if (!isset($document[$fieldHandling])) {
                unset($extraSubmit[$field]);
                continue;
            }
        }

        $record = array_merge($record, $extraSubmit);
        $recordClean = Html::cleanArrayHtml($record);

        /**
         * Select use multiple mode
         */
        $multiple = false;
        $recordList = [$record];
        $recordCleanList = [$recordClean];

        if (isset($record[$multipleField])) {
            $multiple = true;
            $recordList = $recordCleanList = [];
            foreach ($record[$multipleField] as $item) {
                $record[$multipleField] = $item;
                array_push($recordList, $record);
            }
            foreach ($recordClean[$multipleField] as $item) {
                $recordClean[$multipleField] = $item;
                array_push($recordCleanList, $recordClean);
            }
        }

        /**
         * Handler by validator type
         */
        foreach ($recordList as $key => $item) {
            foreach ($item as $field => $value) {

                if (!$persistAnnotationHandling[$field]['html']) {
                    $value = $recordCleanList[$key][$field];
                }

                /**
                 * validator type
                 */
                $type = $persistAnnotationHandling[$field]['validatorType'];
                if (strpos($type, 'int') !== false) {
                    $recordList[$key][$field] = intval($value);
                } elseif (!is_null($value)) {
                    $recordList[$key][$field] = strval($value);
                }
            }
        }

        return $multiple ? $recordList : current($recordList);
    }

    /**
     * Persistence to MySQL
     *
     * @param array $submit
     * @param array $record
     * @param array $original
     * @param array $persistAnnotationHandling
     * @param array $extraSubmit
     * @param array $recordBefore
     * @param array $recordAdd
     * @param array $recordDel
     *
     * @return Output
     * @throws
     */
    protected function persistence(
        array $submit,
        array $record,
        array $original,
        array $persistAnnotationHandling,
        array $extraSubmit,
        array $recordBefore,
        array $recordAdd,
        array $recordDel
    ): ArgsOutput {

        if (empty($this->entity)) {
            throw new ModuleException('Entity is required for persistence module');
        }

        $pk = $this->repository->pk();
        $newly = empty($record[$pk]);

        $result = $this->repository->transactional(
            function () use (
                $submit,
                $record,
                $original,
                $persistAnnotationHandling,
                $extraSubmit,
                $recordBefore,
                $recordAdd,
                $recordDel,
                $newly,
                $pk
            ) {
                /**
                 * Before persistence
                 */

                $arguments = $this->arguments(
                    compact(
                        'newly',
                        'record',
                        'original',
                        'submit',
                        'extraSubmit',
                        'recordBefore',
                        'recordAdd',
                        'recordDel'
                    ),
                    $this->input->args
                );

                $before = $this->caller(
                    $this->method,
                    self::BEFORE_PERSISTENCE,
                    [Message::class, Error::class, true],
                    null,
                    $arguments
                );

                if ($before instanceof Error) {
                    throw new LogicException($before->tiny());
                }

                if (($before instanceof Message) && !$before->isSuccessClassify()) {
                    $message = $this->web->messageLang($before->getMessage(), $before->getArgs());
                    throw new LogicException($message);
                }

                if ($newly) {

                    /**
                     * Newly record
                     */
                    $multipleField = null;
                    foreach ($persistAnnotationHandling as $field => $item) {
                        if (is_array($record[$field] ?? null)) {
                            $multipleField = $field;
                            break; // multiple allow one only
                        }
                    }

                    $loggerType = $multipleField ? 2 : 1;
                    $recordBefore = $recordAdd = $recordDel = [];
                    $record = $this->recordHandler(
                        $record,
                        $record,
                        $persistAnnotationHandling,
                        $extraSubmit,
                        $multipleField
                    );

                    if ($multipleField) {
                        $result = $this->repository->newlyMultiple($record);
                    } else {
                        $result = $this->repository->newly($record);
                    }

                } else {

                    /**
                     * Modify by id
                     */
                    $loggerType = 3;
                    $record = $this->recordHandler($submit, $record, $persistAnnotationHandling, $extraSubmit);
                    $result = $this->repository->modify([$pk => Helper::dig($record, $pk)], $record);
                }

                if ($result === false) {
                    [$error, $flag] = $this->repository->pop(true);
                    if (strpos($flag, Abs::TAG_ROLL) === 0) {
                        throw new LogicException($error);
                    } elseif (strpos($flag, Abs::TAG_VALIDATOR) !== false) {
                        throw new ValidatorException($error);
                    } else {
                        throw new RepositoryException($error);
                    }
                }

                $recordDiff = [
                    Abs::RECORD_LOGGER_ADD    => $recordAdd,
                    Abs::RECORD_LOGGER_DEL    => $recordDel,
                    Abs::RECORD_LOGGER_EFFECT => $result,
                ];
                $record[Abs::RECORD_LOGGER_EXTRA] = $extraSubmit;
                $this->web->databaseOperationLogger($this->entity, $loggerType, $recordBefore, $record, $recordDiff);

                unset($record[Abs::RECORD_LOGGER_EXTRA]);

                /**
                 * After persistence
                 */

                $arguments = $this->arguments(
                    compact(
                        'newly',
                        'record',
                        'original',
                        'submit',
                        'extraSubmit',
                        'recordBefore',
                        'recordAdd',
                        'recordDel',
                        'result'
                    ),
                    ['pk' => $newly ? $result : ($recordBefore[$pk] ?? $original[$pk])],
                    $this->input->args
                );

                $after = $this->caller(
                    $this->method,
                    self::AFTER_PERSISTENCE,
                    [Message::class, Error::class, true],
                    null,
                    $arguments
                );

                if ($after instanceof Error) {
                    throw new LogicException($after->tiny());
                }

                if (($after instanceof Message) && !$after->isSuccessClassify()) {
                    $message = $this->web->messageLang($after->getMessage(), $after->getArgs());
                    throw new LogicException($message);
                }

                // destroy form token
                if ($token = $this->input->submit[Abs::FLAG_WEBSITE_TOKEN] ?? null) {
                    $this->web->invalidWebsiteToken($token);
                }

                return [$result, $before, $after];
            }
        );

        /**
         * Handle error
         */
        if ($result === false) {
            return $this->showError($this->repository->pop());
        }

        [$result, $before, $after] = $result;
        $args = array_merge(
            $this->input->i18nArgs,
            [
                '{{ result }}' => $result,
                '{{ before }}' => $before,
                '{{ after }}'  => $after,
            ]
        );

        $nextRoute = $this->input->nextRoute;
        if (!empty($this->input->sets['nextRouteFn']) && is_callable($this->input->sets['nextRouteFn'])) {
            $nextRoute = call_user_func_array($this->input->sets['nextRouteFn'], [$submit, $record, $original]);
        }

        return $this->showSuccess(
            $newly ? $this->input->i18nNewly : $this->input->i18nModify,
            $this->input->sets,
            $args,
            isset($this->input->sets['function']) ? null : $nextRoute
        );
    }

    /**
     * @return ArgsOutput
     * @throws
     */
    public function logic(): ArgsOutput
    {
        $output = new Output($this->input);

        /**
         * handle annotation
         */

        $result = $this->getPersistenceData();
        if ($result instanceof ArgsOutput) {
            return $result;
        }

        [$submit, $record, $extraSubmit, $recordBefore, $recordAdd, $recordDel] = $result;

        // get annotation
        $result = $this->handleAnnotation($record);
        if ($result instanceof ArgsOutput) {
            return $result;
        }

        [$persistAnnotation, $persistAnnotationHandling, $hooks] = $result;
        $result = $this->handlePersistenceData($persistAnnotation, $record, $hooks, $output);
        if ($result instanceof ArgsOutput) {
            return $result;
        } else {
            [$record, $operates, $format, $original] = $result;
        }

        if ($this->input->submit) {

            // handle by framework
            if (!$this->input->customHandler) {
                $token = $this->input->submit[Abs::FLAG_WEBSITE_TOKEN] ?? null;
                if ($token && !$this->web->validWebsiteToken($token)) {
                    return $this->showError('Form submission exception or so often', ErrorRequestOften::CODE);
                }

                if (!$this->entity) {
                    throw new ModuleException('Automatic persistence and you must configured the entity');
                }

                /**
                 * Rules validator
                 */
                foreach ($persistAnnotationHandling as $field => $item) {
                    $rules = $item['rules'];
                    if (empty($rules)) {
                        continue;
                    }
                    $value = $this->input->submit[$field] ?? null;
                    $result = $this->web->validator($field, $value, $rules, ['label' => $item['label']]);
                    if ($result === false) {
                        return $this->showError($this->web->pop(), ErrorParameter::CODE);
                    }
                }

                return $this->persistence(
                    $submit,
                    $record,
                    $original,
                    $persistAnnotationHandling,
                    $extraSubmit,
                    $recordBefore,
                    $recordAdd,
                    $recordDel
                );

            } else {

                // handle by custom
                $submitCleanList = Html::cleanArrayHtml($this->input->submit);
                foreach ($this->input->submit as $field => $value) {
                    if (!($persistAnnotationHandling[$field]['html'] ?? false)) {
                        $this->input->submit[$field] = $submitCleanList[$field];
                    }
                }

                $arguments = $this->arguments(
                    [
                        'submit'                => $this->input->submit,
                        'persistenceAnnotation' => $persistAnnotationHandling,
                    ],
                    $this->input->args
                );
                $result = $this->caller(
                    $this->method,
                    $fn = self::CUSTOM_HANDLER,
                    [Message::class, Error::class],
                    null,
                    $arguments
                );

                if (empty($result)) {
                    throw new ModuleException(
                        "Persistence {$this->class}::{$this->method}{$fn}() must be implementation"
                    );
                }

                if ($result instanceof Error) {
                    return $this->showError($result->tiny());
                }

                /**
                 * @var Message $result
                 */

                if ($result->isSuccessClassify() && $token = $this->input->submit[Abs::FLAG_WEBSITE_TOKEN] ?? null) {
                    $this->web->invalidWebsiteToken($token);
                }

                return $this->showMessage($result);
            }
        }

        /**
         * assign variable to output
         */

        $fillData = $this->web->getArgs($this->input->fill) ?? [];
        $fillData = Helper::urlDecodeValues(Helper::numericValues($fillData));

        foreach ($record as $key => $item) {
            /**
             * @var Form $form
             */
            $form = $item['type'];
            if (isset($fillData[$key])) {
                $form->setValue($fillData[$key]);
            }
            if (get_class($form) == CkEditor::class) {
                $this->web->appendSrcJsWithKey(
                    'ck-editor-lang',
                    Abs::JS_EDITOR_BUILD_LANG[$this->web->langLatest($this->web->langMap)]
                );
                $this->web->appendSrcJsWithKey('ck-editor', Abs::JS_EDITOR_BUILD);
                $this->web->appendSrcJsWithKey('ck-editor-custom', Abs::JS_EDITOR_CUSTOM);
                $this->web->appendSrcCssWithKey('ck-editor-custom', Abs::CSS_EDITOR_CUSTOM);
            }
        }

        $output->record = $record;
        $output->operates = $operates;
        $output->formatJson = Helper::jsonFlexible($format);

        $output->fileListKeyCollectJson = Helper::jsonFlexible($output->fileListKeyCollect);
        $output->uploadTipsCollectJson = Helper::jsonFlexible($output->uploadTipsCollect);
        $output->fieldHideCollectJson = Helper::jsonFlexible($output->fieldHideCollect);
        $output->fieldDisabledCollectJson = Helper::jsonFlexible($output->fieldDisabledCollect);
        $output->transferKeysCollectJson = Helper::jsonFlexible($output->transferKeysCollect);
        $output->varNameForMetaCollectJson = Helper::jsonFlexible($output->varNameForMetaCollect);

        $output->styleJson = Helper::jsonFlexible($output->style);
        $output->operateStyleJson = Helper::jsonFlexible($output->operateStyle);

        $output = $this->caller(
            $this->method(),
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'), $this->input->args)
        );

        return $output;
    }
}
