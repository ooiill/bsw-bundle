<?php

namespace Leon\BswBundle\Module\Validator;

use Leon\BswBundle\Component\Helper;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class Validator
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $error;

    /**
     * Validator constructor.
     *
     * @param mixed               $value
     * @param array               $args
     * @param TranslatorInterface $translator
     * @param string              $locale
     * @param callable            $argsHandler
     */
    public function __construct(
        $value,
        array $args,
        TranslatorInterface $translator,
        string $locale,
        ?callable $argsHandler = null
    ) {

        $this->value = $value;
        $this->args = $args;
        $this->translator = $translator;
        $this->locale = $locale;

        if (is_callable($this->args)) {
            $this->args = call_user_func($this->args);
        }

        if ($argsHandler) {
            $this->args = call_user_func_array($argsHandler, [$this->args]);
        }

        if ($this->transArgs()) {
            $this->args = Helper::recursionValueHandler(
                $this->args,
                function ($v) {
                    return $this->translator->trans($v, [], 'enum', $this->locale);
                }
            );
        }
    }

    /**
     * Description for current validator
     *
     * @return string
     */
    abstract public function description(): string;

    /**
     * Message for error
     *
     * @return string
     */
    abstract public function message(): string;

    /**
     * Prove the value
     *
     * @param array $extra
     *
     * @return bool
     */
    abstract protected function prove(array $extra = []): bool;

    /**
     * Prove the args
     *
     * @return bool
     */
    protected function proveArgs(): bool
    {
        return true;
    }

    /**
     * Trans the args
     *
     * @return bool
     */
    protected function transArgs(): bool
    {
        return true;
    }

    /**
     * Handle value when prove return true
     *
     * @return mixed
     */
    abstract protected function handler();

    /**
     * Is required for document
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return true;
    }

    /**
     * Array args
     *
     * @return array
     */
    public function arrayArgs(): array
    {
        return $this->args;
    }

    /**
     * Stringify args
     *
     * @return string
     */
    public function stringArgs(): string
    {
        return Helper::printArray($this->args, '[%s]', false);
    }

    /**
     * Validator
     *
     * @param array $extraArgs
     *
     * @return array
     */
    public function validator(array $extraArgs = []): array
    {
        if (!$this->proveArgs()) {
            return [null, 'rules for {{ field }} illegal with {{ args }}'];
        }

        $result = $this->prove($extraArgs);

        $this->value = $result ? $this->handler() : $this->value;
        $this->error = $result ? null : $this->message();

        return [$this->value, $this->error];
    }
}