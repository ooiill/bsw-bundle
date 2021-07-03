<?php

namespace Leon\BswBundle\Module\Bsw\Chart;

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
    const CHART_ITEMS = 'ChartItems';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'chart';
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
            ['condition' => $this->input->condition, 'data' => $this->input->data],
            $this->input->args
        );
        $result = $this->caller(
            $this->method,
            self::CHART_ITEMS,
            [Message::class, Error::class, Abs::T_ARRAY],
            [],
            $arguments
        );

        if ($result instanceof Error) {
            return $this->showError($result->tiny());
        } elseif ($result instanceof Message) {
            return $this->showMessage($result);
        } else {
            $output->items = $result;
        }

        // resource
        $this->web->appendSrcJsWithKey('e-charts', Abs::JS_CHART);
        foreach ($output->items as $item) {
            $this->web->appendSrcJsWithKey('e-charts-theme', $item->getTheme());
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