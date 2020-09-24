<?php

namespace Leon\BswBundle\Module\Bsw\Welcome;

use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const WELCOME = 'Welcome';

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'welcome';
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

        $output->speech = $this->caller(
            $this->method,
            self::WELCOME,
            Abs::T_STRING,
            null,
            $this->arguments($this->input->args)
        );

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