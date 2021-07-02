<?php

namespace Leon\BswBundle\Module\Bsw\Tabs;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Scene\Links;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const TABS_LINKS = 'TabsLinks';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'tabs';
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

        // links
        $links = $this->caller($this->method, self::TABS_LINKS, Abs::T_ARRAY, [], $this->arguments($this->input->args));
        foreach ($links as $item) {
            $fn = $this->method . self::TABS_LINKS;
            Helper::objectInstanceOf(
                $item,
                Links::class,
                "{$this->class}::{$fn}(): array returned array'items"
            );

            /**
             * @var Links $item
             */
            $item->setScript(Html::scriptBuilder($item->getClick(), $item->getArgs()));
            $item->setUrl($this->web->urlSafe($item->getRoute(), $item->getArgs(), 'Build tabs links'));
            array_push($output->links, $item);
        }

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