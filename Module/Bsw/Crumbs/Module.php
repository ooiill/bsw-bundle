<?php

namespace Leon\BswBundle\Module\Bsw\Crumbs;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'crumbs';
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
     */
    public function logic(): ArgsOutput
    {
        $output = new Output($this->input);
        $this->web->crumbs = $this->web->crumbsBuilder(
            $this->input->route,
            array_merge($this->input->masterMenuDetail, $this->input->slaveMenuDetail)
        );

        $getCrumbs = $this->web->getDecodeArgs('crumbs') ?? [];
        $this->web->anyCrumbs($getCrumbs);

        $this->web->correctCrumbs();
        $output->list = $this->web->crumbs;

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