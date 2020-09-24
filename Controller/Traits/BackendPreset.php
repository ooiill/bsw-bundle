<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Bsw\Preview\Entity\Charm;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Exception\FilterException;
use Leon\BswBundle\Module\Filter\Dispatcher as FilterDispatcher;
use Symfony\Contracts\Translation\TranslatorInterface;
use Monolog\Logger;

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
     *
     * @return array
     */
    public function clonePreviewToForm(array $hooked): array
    {
        $hooked = Helper::arrayRemove($hooked, ['id']);
        foreach ($hooked as &$value) {
            if (!is_scalar($value)) {
                continue;
            }
            $value = ltrim($value, '$￥');
        }

        return ['fill' => $hooked];
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
}