<?php

namespace Leon\BswBundle\Module\Bsw\Operate;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Scene\Choice;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const OPERATES = 'Operates';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'operate';
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * @return ArgsOutput
     * @throws
     */
    public function logic(): ArgsOutput
    {
        $output = new Output($this->input);

        $buttonScene = [];
        $choiceScene = [
            Abs::SCENE_IFRAME => new Choice(),
            Abs::SCENE_NORMAL => new Choice(),
        ];

        $arguments = $this->arguments(
            ['condition' => $this->input->condition],
            $this->input->args
        );

        $nowScene = $this->input->iframe ? Abs::SCENE_IFRAME : Abs::SCENE_NORMAL;
        $buttons = $this->caller($this->method, self::OPERATES, Abs::T_ARRAY, [], $arguments);
        $coverArgs = $this->web->parameters('cover_iframe_args_by_name') ?? [];
        $size = $this->getInputAuto('size');

        // buttons handler
        foreach ($buttons as $button) {

            $buttonCls = Button::class;
            if (!Helper::extendClass($button, $buttonCls, true)) {
                $fn = self::OPERATES;
                throw new ModuleException("{$this->class}::{$this->method}{$fn}() return must be {$buttonCls}[]");
            }

            /**
             * @var Button $button
             */
            if ($name = $button->getName()) {
                $buttonArgs = array_merge($button->getArgs(true), $coverArgs[$name] ?? []);
                $button->setArgs($buttonArgs);
            }

            $button->setSize($size);
            $scene = $button->getScene();
            if ($scene === Abs::SCENE_COMMON) {
                $scene = $nowScene;
            }

            // choice
            if ($selector = $button->getSelector()) {
                $choiceScene[$scene]->setEnable()->setMultiple($selector === Abs::SELECTOR_CHECKBOX);
            }

            // script
            $button->setScript(Html::scriptBuilder($button->getClick(), $button->getArgs()));
            $button->setUrl($this->web->urlSafe($button->getRoute(), $button->getArgs(), 'Build page operate'));

            if (!$this->web->routeIsAccess($button->getRouteForAccess())) {
                $button->setDisplay(false);
            }

            $buttonScene[$scene][] = $button;
        }

        $output->choice = $choiceScene[$nowScene] ?? $output->choice;
        $output->buttons = $buttonScene[$nowScene] ?? $output->buttons;
        $output->clsName = $this->getInputAuto('clsName');

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
