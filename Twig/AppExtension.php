<?php

namespace Leon\BswBundle\Twig;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Form\Form;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * Register filters
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('arrayMap', [Helper::class, 'arrayMap']),
            new TwigFilter('icon', [$this, 'icon']),
            new TwigFilter('nativeIcon', [$this, 'nativeIcon']),
            new TwigFilter('implode', [$this, 'implode']),
            new TwigFilter('imageStyle', [$this, 'imageStyle']),
            new TwigFilter('stringify', [$this, 'stringify']),
            new TwigFilter('joinClass', [$this, 'joinClass']),
            new TwigFilter('vueType', [$this, 'vueType']),
            new TwigFilter('formArea', [$this, 'formArea']),
            new TwigFilter('tplReplace', [$this, 'tplReplace']),
            new TwigFilter('phpNativeFn', [$this, 'phpNativeFn']),
        ];
    }

    /**
     * Get icon html
     *
     * @param string $icon
     * @param bool   $html
     * @param array  $class
     * @param array  $queryArr
     *
     * @return string|null
     */
    public static function icon(?string $icon, bool $html = true, array $class = [], array $queryArr = [])
    {
        $flag = 'a';
        $query = null;

        if (strpos($icon, ':') !== false) {
            [$flag, $icon, $query] = explode(':', $icon) + [2 => null];
        }

        if (!$html) {
            return $icon;
        }

        $queryItem = [];
        parse_str($query, $queryItem);
        foreach ($queryItem as &$value) {
            $value = is_null($value) ? true : $value;
        }

        return Html::tag(
            "{$flag}-icon",
            null,
            array_merge(
                $queryItem,
                $queryArr,
                [
                    'type'  => $icon,
                    'class' => $class,
                ]
            )
        );
    }

    /**
     * Get native icon html
     *
     * @param string $icon
     * @param array  $class
     *
     * @return string|null
     */
    public static function nativeIcon(string $icon, array $class = [])
    {
        $flag = 'a';
        if (strpos($icon, ':') !== false) {
            [$flag, $icon] = explode(':', $icon) + [2 => null];
        }

        if ($flag !== 'b') {
            return '';
        }

        array_unshift($class, 'bsw-icon');

        return Html::tag(
            'svg',
            Html::tag('use', null, ['xlink:href' => "#{$icon}"]),
            [
                'class'       => $class,
                'aria-hidden' => true,
            ]
        );
    }

    /**
     * Implode after array_filter
     *
     * @param array  $source
     * @param string $split
     *
     * @return string
     */
    public static function implode(array $source, string $split = null): string
    {
        return implode($split, array_filter($source));
    }

    /**
     * Image style
     *
     * @param array  $config
     * @param string $flag
     * @param bool   $boundary
     *
     * @return string
     */
    public static function imageStyle(array $config, string $flag, bool $boundary = false): string
    {
        $map = [
            'image'    => 'url(%s) !important',
            'repeat'   => '%s !important',
            'color'    => '%s !important',
            'position' => '%s !important',
            'size'     => '%s !important',
        ];

        $attributes = [];

        foreach ($map as $tag => $tpl) {
            $key = Helper::underToCamel("{$flag}_background_{$tag}");
            if (!empty($config[$key])) {
                $keyHandling = "background-{$tag}";
                $val = sprintf($tpl, $config[$key]);
                array_push($attributes, "{$keyHandling}: {$val}");
            }
        }

        $attributes = implode('; ', $attributes);

        return $boundary ? "{ {$attributes} }" : $attributes;
    }

    /**
     * @param        $target
     * @param string $default
     * @param int    $jsonFlag
     *
     * @return string
     */
    public static function stringify($target, string $default = '', int $jsonFlag = 0)
    {
        $stringify = Helper::jsonStringify($target, $default, $jsonFlag);

        return preg_replace('/[\'|"]{var:(.*)}[\'|"]/', "$1", $stringify);
    }

    /**
     * Join class
     *
     * @param array $class
     *
     * @return string
     */
    public static function joinClass(array $class): string
    {
        return implode(' ', array_unique(array_filter($class)));
    }

    /**
     * Vue type
     *
     * @param $target
     *
     * @return string
     */
    public static function vueType($target)
    {
        if (is_numeric($target)) {
            return $target;
        }

        return "'{$target}'";
    }

    /**
     * Form area
     *
     * @param Form|array  $type
     * @param string|null $area
     *
     * @return bool
     */
    public static function formArea($type, ?string $area = null): bool
    {
        if (empty($area)) {
            return true;
        }

        if ($type instanceof Form) {
            return $type->getAllowArea($area);
        }

        if (is_array($type)) {
            $allow = 0;
            foreach ($type as $form) {
                /**
                 * @var Form $form
                 */
                $allow += $form->getAllowArea($area) ? 1 : 0;
            }

            return $allow === count($type);
        }

        return false;
    }

    /**
     * @param string $tpl
     * @param array  $variables
     *
     * @return string
     */
    public static function tplReplace(string $tpl, array $variables): string
    {
        $variables = Helper::arrayMapKey($variables, '{$%s}');
        $tpl = str_replace(array_keys($variables), array_values($variables), $tpl);

        return $tpl;
    }

    /**
     * @param string $fn
     * @param mixed  ...$args
     *
     * @return null
     */
    public static function phpNativeFn(string $fn, ...$args)
    {
        if (!function_exists($fn)) {
            return null;
        }

        return $fn(...$args);
    }
}