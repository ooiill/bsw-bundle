<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Module\Entity\Abs;

trait FormRules
{
    /**
     * Form rule required
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleRequired(string $message = null): array
    {
        $rule = Abs::RULES_REQUIRED;
        $rule['message'] = $message ?? $rule['message'];

        return $rule;
    }

    /**
     * Form rule required
     *
     * @param string $message
     * @param array  $args
     * @param bool   $single
     *
     * @return array
     */
    public function formRuleRequiredMessage(string $message, array $args = [], bool $single = true): array
    {
        $rule = Abs::RULES_REQUIRED;
        $rule['message'] = $this->messageLang($message, $args);

        return $single ? [$rule] : $rule;
    }

    /**
     * Form rule length
     *
     * @param int         $len
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleLen(int $len, string $message = null): array
    {
        return [
            'len'     => $len,
            'message' => $message ?? '{{ field }} Length must equal to {{ arg1 }}',
            'args'    => ['{{ arg1 }}' => $len],
        ];
    }

    /**
     * Form rule min
     *
     * @param int         $min
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleMin(int $min, string $message = null): array
    {
        return [
            'min'     => $min,
            'message' => $message ?? '{{ field }} Length must greater than or equal to {{ arg1 }}',
            'args'    => ['{{ arg1 }}' => $min],
        ];
    }

    /**
     * Form rule max
     *
     * @param int         $max
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleMax(int $max, string $message = null): array
    {
        return [
            'max'     => $max,
            'message' => $message ?? '{{ field }} Length must less than or equal to {{ arg1 }}',
            'args'    => ['{{ arg1 }}' => $max],
        ];
    }

    /**
     * Form rule url
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleUrl(string $message = null): array
    {
        return [
            'type'    => 'url',
            'message' => $message ?? '{{ field }} Must be url',
        ];
    }

    /**
     * Form rule email
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleEmail(string $message = null): array
    {
        return [
            'type'    => 'email',
            'message' => $message ?? '{{ field }} Must be email',
        ];
    }

    /**
     * Form rule string
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleString(string $message = null): array
    {
        return [
            'type'    => 'string',
            'message' => $message ?? '{{ field }} Must be string',
        ];
    }

    /**
     * Form rule number
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleNumber(string $message = null): array
    {
        return [
            'type'    => 'number',
            'message' => $message ?? '{{ field }} Must be numeric',
        ];
    }

    /**
     * Form rule integer
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleInteger(string $message = null): array
    {
        return [
            'type'    => 'integer',
            'message' => $message ?? '{{ field }} Must be integer',
        ];
    }

    /**
     * Form rule float
     *
     * @param string|null $message
     *
     * @return array
     */
    public function formRuleFloat(string $message = null): array
    {
        return [
            'type'    => 'float',
            'message' => $message ?? '{{ field }} Must be float',
        ];
    }

    /**
     * Form rule pattern
     *
     * @param string $pattern
     * @param string $message
     *
     * @return array
     */
    public function formRulePattern(string $pattern, string $message): array
    {
        return [
            'pattern' => $pattern,
            'message' => $message,
        ];
    }
}