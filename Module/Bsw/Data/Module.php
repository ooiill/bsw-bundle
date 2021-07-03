<?php

namespace Leon\BswBundle\Module\Bsw\Data;

use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const DATA_GENERATOR = 'DataGenerator';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'data';
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

        // items
        $arguments = $this->arguments(
            [
                'condition' => $this->input->condition,
                'query'     => $this->input->query,
            ],
            $this->input->args
        );

        $result = $this->caller(
            $this->method,
            self::DATA_GENERATOR,
            [Message::class, Error::class, Abs::T_ARRAY],
            [],
            $arguments
        );

        if ($result instanceof Error) {
            return $this->showError($result->tiny());
        } elseif ($result instanceof Message) {
            return $this->showMessage($result);
        } else {
            $output->data = $result;
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