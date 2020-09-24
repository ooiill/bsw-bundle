<?php

namespace Leon\BswBundle\Module\Bsw;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Reflection;
use Leon\BswBundle\Controller\BswWebController;
use Leon\BswBundle\Module\Exception\ModuleException;
use ReflectionClass;

class Dispatcher
{
    /**
     * @var BswWebController
     */
    protected $web;

    /**
     * Dispatcher constructor.
     *
     * @param BswWebController $web
     */
    public function __construct(BswWebController $web)
    {
        $this->web = $web;
    }

    /**
     * Execute
     *
     * @param string $moduleClass
     * @param array  $globalArgs
     * @param array  $acmeArgs
     * @param array  $routeArgs
     * @param array  $extraArgs
     * @param array  $beforeOutput
     *
     * @return array
     * @throws
     */
    public function execute(
        string $moduleClass,
        array $globalArgs,
        array $acmeArgs,
        array $routeArgs,
        array $extraArgs,
        array $beforeOutput
    ): array {

        if (!Helper::extendClass($moduleClass, Bsw::class)) {
            throw new ModuleException("Class {$moduleClass} should extend " . Bsw::class);
        }

        /**
         * @var Bsw $bsw
         */
        $bsw = new $moduleClass($this->web);
        $input = $bsw->input();
        $exclude = $bsw->inheritExcludeArgs();

        if ($exclude === true) {
            $ref = new ReflectionClass($input);
            $exclude = [];
            foreach ($ref->getProperties() as $property) {
                if ($property->class === $ref->name) {
                    $exclude[] = $property->name;
                }
            }
        }

        if (is_array($exclude)) {
            Helper::arrayPop($beforeOutput, $exclude);
        }

        $acmeArgs = array_merge($globalArgs, $acmeArgs, $routeArgs, $extraArgs, $beforeOutput);

        if (($acmeArgs['ajax'] ?? false) && !$bsw->allowAjax()) {
            return [null, null, [], []];
        }

        if (($acmeArgs['iframe'] ?? false) && !$bsw->allowIframe()) {
            return [null, null, [], []];
        }

        /**
         * create input args
         */
        $inputReal = [];

        $cls = get_class($input);
        $ref = new Reflection();

        foreach ($input as $attribute => $value) {
            if (array_key_exists($attribute, $acmeArgs)) {
                $input->{$attribute} = $acmeArgs[$attribute];
            }
            if ($ref->propertyExistsSelf($cls, $attribute)) {
                $inputReal[$attribute] = $input->{$attribute};
            }
        }

        /**
         * handle output args
         */
        $bsw->initialization($input);
        $output = Helper::entityToArray($bsw->logic());

        /**
         * source
         */
        $this->web->appendSrcCss($bsw->css());
        $this->web->appendSrcJs($bsw->javascript());
        $input = Helper::entityToArray($input);

        return [$bsw->name(), $bsw->twig(), $input, $output];
    }
}