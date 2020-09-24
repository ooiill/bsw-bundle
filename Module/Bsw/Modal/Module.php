<?php

namespace Leon\BswBundle\Module\Bsw\Modal;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @return bool|array
     */
    public function inheritExcludeArgs()
    {
        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'modal';
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

        $output->bodyStyleJson = Helper::jsonFlexible($this->input->bodyStyle);
        $output->maskStyleJson = Helper::jsonFlexible($this->input->maskStyle);
        $output->dialogStyleJson = Helper::jsonFlexible($this->input->dialogStyle);

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