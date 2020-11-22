<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property AbstractController  $container
 * @property TranslatorInterface $translator
 */
trait WebSource
{
    /**
     * @var array
     */
    protected $srcExtraPrefixMap = [];

    /**
     * @var array
     * @license
     *   split : for you project
     *   split ; for bsw bundle
     */
    protected $srcPrefixMap = [

        // for your project
        'odd:' => [
            'tpl' => '/%s',
            'var' => ['src'],
        ],
        'diy:' => [
            'tpl' => '/dist/%s%s%s',
            'var' => ['dist', 'src', 'version'],
        ],
        'npm:' => [
            'tpl' => '/node_modules/%s',
            'var' => ['src'],
        ],

        // for bsw-bundle
        'odd;' => [
            'tpl' => '/bundles/leonbsw/%s',
            'var' => ['src'],
        ],
        'diy;' => [
            'tpl' => '/bundles/leonbsw/dist/%s%s%s',
            'var' => ['dist', 'src', 'version'],
        ],
        'npm;' => [
            'tpl' => '/bundles/leonbsw/node_modules/%s',
            'var' => ['src'],
        ],
    ];

    /**
     * @var array
     */
    protected $mapCdnSrcCss = [];

    /**
     * @var array
     */
    protected $initialSrcCss = [
        'ant-d'   => Abs::CSS_ANT_D,
        'animate' => Abs::CSS_ANIMATE,
    ];

    /**
     * @var array
     */
    protected $currentSrcCss = [];

    /**
     * @var array
     */
    protected $initialPositionSrcCss = [
        'ant-d'   => Abs::POS_TOP,
        'animate' => Abs::POS_TOP,
    ];

    /**
     * @var array
     */
    protected $currentPositionSrcCss = [];

    /**
     * @var array
     */
    protected $mapCdnSrcJs = [];

    /**
     * @var array
     */
    protected $initialSrcJs = [
        'jquery' => Abs::JS_JQUERY,
        'moment' => Abs::JS_MOMENT,
        'vue'    => Abs::JS_VUE_MIN,
        'ant-d'  => Abs::JS_ANT_D_LANG_MIN,
        'app'    => Abs::JS_FOUNDATION,
    ];

    /**
     * @var array
     */
    protected $currentSrcJs = [];

    /**
     * @var array
     */
    protected $initialPositionSrcJs = [
        'jquery' => Abs::POS_TOP,
        'moment' => Abs::POS_TOP,
        'vue'    => Abs::POS_TOP,
        'ant-d'  => Abs::POS_TOP,
        'app'    => Abs::POS_TOP,
    ];

    /**
     * @var array
     */
    protected $currentPositionSrcJs = [];

    /**
     * Source handler
     *
     * @return array
     */
    public function source(): array
    {
        [$controller, $method] = $this->getMCM('-');

        $default = "diy:{$controller}/{$method}";
        $version = $this->debug ? mt_rand() : $this->parameter('version');

        $source = [];
        foreach ([Abs::SRC_CSS, Abs::SRC_JS] as $suffix) {

            $type = ucfirst($suffix);
            $initialSrc = "initialSrc{$type}";
            $currentSrc = "currentSrc{$type}";
            $initialPosition = "initialPositionSrc{$type}";
            $currentPosition = "currentPositionSrc{$type}";

            $this->{$currentSrc} = array_merge($this->{$initialSrc}, $this->{$currentSrc});
            $this->{$currentSrc} = array_filter(array_unique($this->{$currentSrc}));
            $this->{$currentPosition} = array_merge($this->{$initialPosition}, $this->{$currentPosition});

            foreach ($this->{$currentSrc} as $key => &$src) {
                $posKey = is_numeric($key) ? $src : $key;
                $src = ($src === true) ? $default : $src;
                $src = $this->perfectSourceUrl($src, $suffix, $key, $version);
                $pos = $this->{$currentPosition}[$posKey] ?? Abs::POS_TOP;
                $source[$suffix][$pos][] = $src;
            }
        }

        return $source;
    }

    /**
     * Perfect source url
     *
     * @param string $src
     * @param string $suffix
     * @param mixed  $cdnKey
     * @param string $version
     *
     * @return string
     */
    public function perfectSourceUrl(
        string $src,
        ?string $suffix = null,
        ?string $cdnKey = null,
        ?string $version = null
    ) {
        if ($suffix && !Helper::strEndWith($src, ".{$suffix}")) {
            $src = "{$src}.{$suffix}";
        }

        if (Helper::isUrlAlready($src)) {
            return $src;
        }

        if (empty($cdnKey) || is_numeric($cdnKey)) {
            $cdnKey = $src;
        }

        $cdn = 'mapCdnSrc';
        if ($suffix) {
            $cdn .= ucfirst($suffix);
        }

        if (isset($this->{$cdn}[$cdnKey])) {
            return $this->{$cdn}[$cdnKey];
        }

        return $this->caching(
            function () use ($src, $suffix, $version) {

                if ($suffix) {
                    $dist = $this->debug ? "src-{$suffix}/" : "{$suffix}/";
                } else {
                    $dist = 'src/';
                }

                if ($version) {
                    $version = "?version={$version}";
                }

                $len = 4;
                $flag = substr($src, 0, $len);
                $prefixMap = array_merge($this->srcPrefixMap, $this->srcExtraPrefixMap);

                if ($prefixMap[$flag] ?? null) {
                    $flag = $prefixMap[$flag];
                    $src = substr($src, $len);
                } else {
                    $flag = $prefixMap['odd:'];
                }

                $variables = [];
                foreach ($flag['var'] as $var) {
                    $variables[] = $$var;
                }

                return sprintf($flag['tpl'], ...$variables);
            }
        );
    }

    /**
     * Append css to stack with non-key
     *
     * @param array|string $css
     * @param string       $position
     * @param string       $insert
     * @param bool         $before
     *
     * @return void
     */
    public function appendSrcCss($css, string $position = Abs::POS_BOTTOM, ?string $insert = null, bool $before = false)
    {
        $css = (array)$css;

        $position = Helper::arrayValuesSetTo($css, $position, true);
        $this->currentPositionSrcCss = array_merge($position, $this->currentPositionSrcCss);

        if (isset($insert)) {
            $this->currentSrcCss = Helper::arrayInsertAssoc($this->currentSrcCss, $insert, $css, $before);
        } else {
            $this->currentSrcCss = array_merge($this->currentSrcCss, $css);
        }
    }

    /**
     * Append css to stack with key
     *
     * @param string      $key
     * @param string|bool $css
     * @param string      $position
     * @param string      $insert
     * @param bool        $before
     *
     * @return void
     */
    public function appendSrcCssWithKey(
        string $key,
        $css,
        string $position = Abs::POS_BOTTOM,
        ?string $insert = null,
        bool $before = false
    ) {
        if (isset($insert)) {
            $this->currentSrcCss = Helper::arrayInsertAssoc($this->currentSrcCss, $insert, [$key => $css], $before);
        } else {
            $this->currentSrcCss[$key] = $css;
        }

        if (!isset($this->positionSrcCss[$key])) {
            $this->currentPositionSrcCss[$key] = $position;
        }
    }

    /**
     * Remove css by keys
     *
     * @param array|string $keys
     */
    public function removeSrcCss($keys)
    {
        foreach ((array)$keys as $key) {
            $this->appendSrcCssWithKey($key, null);
        }
    }

    /**
     * Append js to stack with non-key
     *
     * @param array|string $js
     * @param string       $position
     * @param string       $insert
     * @param bool         $before
     *
     * @return void
     */
    public function appendSrcJs($js, string $position = Abs::POS_BOTTOM, ?string $insert = null, bool $before = false)
    {
        $js = (array)$js;

        $position = Helper::arrayValuesSetTo($js, $position, true);
        $this->currentPositionSrcJs = array_merge($position, $this->currentPositionSrcJs);

        if (isset($insert)) {
            $this->currentSrcJs = Helper::arrayInsertAssoc($this->currentSrcJs, $insert, $js, $before);
        } else {
            $this->currentSrcJs = array_merge($this->currentSrcJs, $js);
        }
    }

    /**
     * Append js to stack with key
     *
     * @param string      $key
     * @param string|bool $js
     * @param string      $position
     * @param string      $insert
     * @param bool        $before
     *
     * @return void
     */
    public function appendSrcJsWithKey(
        string $key,
        $js,
        string $position = Abs::POS_BOTTOM,
        ?string $insert = null,
        bool $before = false
    ) {
        if (isset($insert)) {
            $this->currentSrcJs = Helper::arrayInsertAssoc($this->currentSrcJs, $insert, [$key => $js], $before);
        } else {
            $this->currentSrcJs[$key] = $js;
        }

        if (!isset($this->positionSrcJs[$key])) {
            $this->currentPositionSrcJs[$key] = $position;
        }
    }

    /**
     * Remove js by keys
     *
     * @param array|string $keys
     */
    public function removeSrcJs($keys)
    {
        foreach ((array)$keys as $key) {
            $this->appendSrcJsWithKey($key, null);
        }
    }

    /**
     * Append css and js
     *
     * @param string|bool $value
     * @param string      $key
     * @param string      $position
     * @param string      $insert
     * @param bool        $before
     *
     * @return void
     */
    public function appendSrc(
        $value,
        ?string $key = null,
        string $position = Abs::POS_BOTTOM,
        ?string $insert = null,
        bool $before = false
    ) {
        $key = $key ?: $this->route;

        $this->appendSrcCssWithKey($key, $value, $position, $insert, $before);
        $this->appendSrcJsWithKey($key, $value, $position, $insert, $before);
    }

    /**
     * Remove css and js by keys
     *
     * @param array|string $keys
     */
    public function removeSrc($keys)
    {
        $this->removeSrcCss($keys);
        $this->removeSrcJs($keys);
    }
}
