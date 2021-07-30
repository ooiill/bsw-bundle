<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Dispatcher as FilterDispatcher;
use Leon\BswBundle\Module\Scene\Charm;
use Symfony\Contracts\Translation\TranslatorInterface;
use Monolog\Logger;
use Exception;

/**
 * @property TranslatorInterface $translator
 * @property Logger              $logger
 */
trait BackendPreset
{
    /**
     * Route name
     *
     * @param Arguments $args
     *
     * @return array
     */
    public function acmeEnumExtraRouteName(Arguments $args): array
    {
        return $this->routeKVP(false);
    }

    /**
     * Command
     *
     * @param Arguments $args
     *
     * @return array
     */
    public function acmeEnumExtraCommand(Arguments $args): array
    {
        $result = $this->commandCaller('list', ['--format' => 'json']);
        $result = Helper::parseJsonString($result);

        $commands = [];
        foreach ($result['commands'] ?? [] as $item) {
            $name = $item['name'];
            foreach ($this->parameters('command_queue_pos') as $pos) {
                if (strpos($name, $pos) === 0) {
                    $commands[$name] = $item['description'];
                }
            }
        }

        return $commands;
    }

    /**
     * Head time
     *
     * @param $current
     * @param $hooked
     * @param $original
     *
     * @return Charm|string
     */
    public function previewCharmHeadTime($current, $hooked, $original)
    {
        if ($original > time()) {
            return new Charm(Abs::HTML_GREEN, $current);
        }

        return new Charm(Abs::HTML_GRAY, $current);
    }

    /**
     * Tail time
     *
     * @param $current
     * @param $hooked
     * @param $original
     *
     * @return Charm|string
     */
    public function previewCharmTailTime($current, $hooked, $original)
    {
        return $this->previewCharmHeadTime($current, $hooked, $original);
    }

    /**
     * Clone to form
     *
     * @param array $hooked
     * @param bool  $needKey
     * @param array $ignoreKeys
     *
     * @return array
     */
    public function clonePreviewToForm(array $hooked, bool $needKey = true, array $ignoreKeys = []): array
    {
        $hooked = Helper::arrayRemove($hooked, ['id']);
        foreach ($hooked as $key => &$value) {
            if (!is_scalar($value)) {
                continue;
            }
            if (!empty($ignoreKeys) && in_array($key, $ignoreKeys)) {
                unset($hooked[$key]);
                continue;
            }
            $value = ltrim($value, '$￥');
        }

        return $needKey ? ['fill' => $hooked] : $hooked;
    }

    /**
     * Html -> upward infect
     *
     * @param string $class
     * @param int    $level
     * @param string $tag
     * @param string $value
     *
     * @return String
     */
    public function getUpwardInfectHtml(string $class, int $level = 3, string $tag = 'p', string $value = null): string
    {
        return Html::tag(
            $tag,
            $value,
            [
                'class'             => 'bsw-upward-infect',
                'data-infect-class' => $class,
                'data-infect-level' => $level,
            ]
        );
    }

    /**
     * Charm -> upward infect
     *
     * @param string $value
     * @param string $class
     * @param int    $level
     * @param string $tag
     *
     * @return Charm
     */
    public function charmUpwardInfect(string $value, string $class, int $level = 3, string $tag = 'p'): Charm
    {
        $element = $this->getUpwardInfectHtml($class, $level, $tag, '{value}');

        return new Charm($element, $value);
    }

    /**
     * Charm -> add class
     *
     * @param string $value
     * @param string $class
     * @param string $tag
     *
     * @return Charm
     */
    public function charmAddClass(string $value, string $class, string $tag = 'p'): Charm
    {
        return new Charm(Html::tag($tag, '{value}', ['class' => $class]), $value);
    }

    /**
     * Get filter from condition
     *
     * @param Arguments  $args
     * @param array|null $fields
     *
     * @return array|Message
     */
    public function getFilterFromCondition(Arguments $args, array $fields = null)
    {
        try {
            $condition = $fields ? Helper::arrayPull($args->condition, $fields) : $args->condition;
            $filter = (new FilterDispatcher())->filterList($condition, FilterDispatcher::DQL_MODE);
        } catch (FilterException $e) {
            return (new Message())
                ->setMessage($e->getMessage())
                ->setClassify(Abs::TAG_CLASSIFY_ERROR)
                ->setCode(ErrorParameter::CODE);
        }

        return $filter;
    }

    /**
     * Create input inline for edit
     *
     * @param        $value
     * @param string $route
     * @param array  $args
     * @param array  $style
     * @param null   $class
     *
     * @return string
     */
    public function createInlineEditInput(
        $value,
        string $route,
        array $args = [],
        array $style = [],
        $class = null
    ): string {

        $class = array_merge(['bsw-inline-edit-input', 'bsw-float-left'], (array)$class);
        $class = array_unique(array_filter($class));
        $input = Html::tag('input', null, ['type' => 'text', 'value' => $value, 'disabled' => true]);

        return Html::tag(
            'div',
            Html::tag('label') . $input,
            [
                'class'    => $class,
                'style'    => $style,
                'title'    => $this->twigLang('Double click for edit mode'),
                'data-api' => $this->url($route),
                'data-bsw' => Helper::jsonStringify($args),
            ]
        );
    }

    /**
     * Create struct for slots tips
     *
     * @param string $tips
     *
     * @return array
     */
    public function slotsTips(string $tips): array
    {
        return [
            'tpl' => Abs::RENDER_TD_TIPS,
            'var' => ['tips' => $tips],
        ];
    }
}