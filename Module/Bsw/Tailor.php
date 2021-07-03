<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Scene\Choice;

abstract class Tailor
{
    /**
     * @var BswWebController
     */
    protected $web;

    /**
     * @var array|string
     */
    protected $field;

    /**
     * @var string
     */
    private $fieldUnder;

    /**
     * @var string
     */
    protected $fieldCamel;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $keyword;

    /**
     * Tailor constructor
     *
     * @param BswWebController $web
     * @param mixed            $field
     */
    public function __construct(BswWebController $web, $field)
    {
        $this->web = $web;
        $this->field = $field;

        if (is_string($this->field)) {
            $this->fieldHandler($this->field);
        }

        $this->initial();
    }

    /**
     * @param string $field
     */
    private function fieldHandler(string $field)
    {
        $this->fieldUnder = Helper::camelToUnder($field);
        $this->fieldCamel = Helper::underToCamel($this->fieldUnder);

        $this->label = Helper::stringToLabel($this->fieldUnder);
        $this->keyword = current(explode('_', $this->fieldUnder));
    }

    /**
     * @param string $salt
     *
     * @return string
     */
    protected function md1(string $salt): string
    {
        return substr(md5(strrev($salt)), 12, 6);
    }

    /**
     * @return void
     */
    protected function initial()
    {
    }

    //
    // == Filter ==
    //

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorFilterAnnotation(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function tailorFilterAnnotationOnly(Arguments $args)
    {
        return $args->target;
    }

    //
    // == Preview ==
    //

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewQuery(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAnnotation(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function tailorPreviewAnnotationOnly(Arguments $args)
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewBeforeHook(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAfterHook(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewBeforeRender(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return Choice
     */
    public function tailorPreviewChoice(Arguments $args): Choice
    {
        return $args->target;
    }

    //
    // == Persistence ==
    //

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPersistenceAnnotation(Arguments $args): array
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function tailorPersistenceAnnotationOnly(Arguments $args)
    {
        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return Error|array
     */
    public function tailorPersistenceAfterSubmit(Arguments $args)
    {
        return [$args->target, $args->extraSubmit ?? []];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPersistenceBeforeRender(Arguments $args): array
    {
        return $args->target;
    }
}