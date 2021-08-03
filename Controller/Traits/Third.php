<?php

namespace Leon\BswBundle\Controller\Traits;

use Carbon\Carbon;
use EasyWeChat\Factory as WxFactory;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Scene\Menu;
use Mexitek\PHPColors\Color;
use Yansongda\Pay\Gateways\Alipay;
use Yansongda\Pay\Pay as WxAliPayment;
use EasyWeChat\OfficialAccount\Application as WxOfficial;
use EasyWeChat\MiniProgram\Application as WxApplet;
use EasyWeChat\Payment\Application as WxPayment;
use EasyWeChat\OpenPlatform\Application as WxOpen;
use EasyWeChat\Work\Application as WxWork;
use EasyWeChat\OpenWork\Application as WxWorkOpen;
use EasyWeChat\MicroMerchant\Application as WxMicro;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Session\Session;
use Gregwar\Captcha\CaptchaBuilder;
use ParagonIE\ConstantTime\Base32;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Monolog\Logger;
use App\Kernel;
use OTPHP\TOTP;
use Parsedown;
use Exception;

/**
 * @property Kernel  $kernel
 * @property Session $session
 * @property Logger  $logger
 */
trait Third
{
    /**
     * Create qr code
     *
     * @param string $content
     * @param int    $qrWidth
     * @param string $level
     * @param int    $qrMargin
     * @param array  $fColor
     * @param array  $bColor
     * @param string $logoFile
     * @param int    $logoWidth
     *
     * @return QrCode
     * @throws
     */
    public function createQrCode(
        string $content,
        int $qrWidth = 256,
        int $qrMargin = 10,
        ?string $level = null,
        ?array $fColor = null,
        ?array $bColor = null,
        ?string $logoFile = null,
        ?int $logoWidth = null
    ) {

        $qrCode = new QrCode($content);
        $qrCode->setSize($qrWidth);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin($qrMargin);
        $qrCode->setEncoding('utf-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel($level ?: ErrorCorrectionLevel::MEDIUM));
        $qrCode->setForegroundColor($fColor ?: ['r' => 70, 'g' => 70, 'b' => 70]);
        $qrCode->setBackgroundColor($bColor ?: ['r' => 255, 'g' => 255, 'b' => 255]);

        if (!empty($logoFile) && realpath($logoFile)) {
            $qrCode->setLogoPath($logoFile);
            $logoWidth = $logoWidth ?: $qrWidth / 4;
            $qrCode->setLogoWidth($logoWidth);
        }

        $qrCode->setValidateResult(false);

        return $qrCode;
    }

    /**
     * Time based One-Time Password Algorithm (T.O.T.P)
     *
     * @param string $uid
     * @param int    $digits
     * @param int    $time
     *
     * @return array
     */
    public function TOTPToken(string $uid, int $digits = 6, int $time = null): array
    {
        $epoch = 0;
        $second = 120;
        $time = $time ?? $this->cnf->totp_token_second ?? $second;
        $time = $time ?: $second;

        $secret = Base32::encode($this->parameter('salt') . $uid);
        $token = TOTP::create($secret, $time, 'sha1', $digits, $epoch);

        $now = time();
        $timeCode = intval(($now - $epoch) / $time);
        $from = $timeCode * $time;
        $to = $from + $time;

        return [
            $token->at($now),
            date(Abs::FMT_FULL, $from),
            date(Abs::FMT_FULL, $to),
        ];
    }

    /**
     * Check captcha
     *
     * @param string $input
     * @param bool   $changeWhenWrong
     *
     * @return bool
     */
    public function checkCaptcha(string $input, bool $changeWhenWrong = true): bool
    {
        $captcha = $this->session->get($this->skCaptcha);
        $this->logger->debug("Number captcha in server {$captcha} and user input {$input}");
        $passed = (new CaptchaBuilder($captcha))->testPhrase($input);
        if (!$passed && $changeWhenWrong) {
            $this->session->set($this->skCaptcha, Helper::randString());
        }

        return $passed;
    }

    /**
     * Human time different
     *
     * @param string $fullDate
     *
     * @return string
     */
    public function humanTimeDiff(string $fullDate): string
    {
        $lang = $this->langLatest(['cn' => 'zh-CN', 'en' => 'en'], 'en');
        $cb = Carbon::createFromFormat(Abs::FMT_FULL, $fullDate);

        return $cb->locale($lang)->diffForHumans();
    }

    /**
     * Name to color
     *
     * @param string $target
     *
     * @return array
     * @throws
     */
    public function nameToColor(string $target): array
    {
        $colorHex = Helper::colorValue($target, true);
        $color = new Color($colorHex);
        $color = $color->isDark() ? $colorHex : "#{$color->darken()}";

        $target = Helper::filterSpecialChar($target, ' ');
        $target = current(explode(' ', $target));

        if (strlen($target) > $max = 6) {
            $target = substr($target, 0, $max);
        }

        return [$target, $color];
    }

    /**
     * Parse markdown content and toc
     *
     * @param int      $seq
     * @param string   $markdownOrFile
     * @param callable $linkHandler
     * @param callable $liHandler
     *
     * @return array
     */
    public function parseMdContentAndToc(
        int $seq,
        string $markdownOrFile,
        callable $linkHandler = null,
        callable $liHandler = null
    ): array {

        $isFile = false;
        $parseMarkdown = new Parsedown();
        if (file_exists($markdownOrFile)) {
            $isFile = true;
            $markdown = file_get_contents($markdownOrFile);
        } else {
            $markdown = $markdownOrFile;
        }

        $toc = [];
        $index = [];
        $content = $parseMarkdown->text($markdown);
        $content = preg_replace_callback(
            '/\<img(.*)src="(.*?)"(.*?)alt="(.*?)"(.*?)>/',
            function ($matches) use ($content) {
                return Html::tag(
                    'a',
                    "<img{$matches[1]}src='{$matches[2]}'{$matches[3]}alt='{$matches[4]}'{$matches[5]}>",
                    [
                        'class'         => 'bsw-preview-image',
                        'data-fancybox' => 'markdown',
                        'href'          => $matches[2],
                        'data-caption'  => $matches[4],
                    ]
                );
            },
            $content
        );
        $content = preg_replace_callback(
            '/\<h([1-3])\>(.*?)\<\/h[1-3]\>/',
            function ($matches) use ($seq, $markdownOrFile, $linkHandler, $liHandler, &$toc, &$index) {
                [$_, $n, $idx] = $matches;
                $id = strtoupper(substr(Helper::generateToken(), 2, 8));
                $link = "#{$id}";

                if ($linkHandler) {
                    $items = call_user_func_array($linkHandler, [$seq, $markdownOrFile, $id, $n, $idx]);
                    Helper::callReturnType($items, Abs::T_ARRAY, 'Handler for `link` of markdown parser');
                    [$link, $idx] = $items;
                }

                $li = Html::tag('li', Html::cleanHtml($idx), ['class' => ["indent-h{$n}"]]);
                $li = Html::tag('a', $li, ['href' => $link, 'id' => "index-{$id}"]);
                if ($liHandler) {
                    $li = call_user_func_array($liHandler, [$markdownOrFile, $id, $n, $idx, $link]);
                    Helper::callReturnType($li, Abs::T_STRING, 'Handler for `li` of markdown parser');
                }

                $index[$id] = $idx;
                array_push($toc, $li);
                $anchor = Html::tag('a', '♪', ['class' => 'anchor', 'href' => $link]);

                return Html::tag("h{$n}", "{$idx}{$anchor}", ['id' => $id]);
            },
            $content
        );

        $titleId = key($index);
        $titleLabel = Helper::dig($index, $titleId);
        $host = Helper::joinString('/', $this->url($this->route), pathinfo($markdownOrFile, PATHINFO_FILENAME));
        $menu = (new Menu())->setLabel(Html::cleanHtml($titleLabel));

        if ($isFile) {
            $menu->setUrl("{$host}#{$titleId}");
        }
        $menuSub = [];
        foreach ($index as $id => $label) {
            $m = (new Menu())->setLabel(Html::cleanHtml($label));
            if ($isFile) {
                $m->setUrl("{$host}#{$id}");
            }
            array_push($menuSub, $m);
        }

        return [
            'toc'     => Html::tag('ul', implode("\n", $toc)),
            'menu'    => $menu,
            'menuSub' => $menuSub,
            'content' => $content,
        ];
    }

    /**
     * Parse markdown in path
     *
     * @param string   $path
     * @param callable $fileCall
     * @param callable $dirCall
     * @param callable $linkHandler
     * @param callable $liHandler
     * @param string   $keySuffix
     *
     * @return array
     */
    public function parseMdInPath(
        string $path,
        ?callable $fileCall = null,
        ?callable $dirCall = null,
        ?callable $linkHandler = null,
        ?callable $liHandler = null,
        ?string $keySuffix = null
    ): array {
        if (!$fileCall) {
            $fileCall = function ($file) {
                return Helper::strEndWith($file, '.md') ? $file : false;
            };
        }
        if (!$dirCall) {
            $dirCall = function ($dir) {
                return false;
            };
        }

        return $this->caching(
            function () use ($path, $fileCall, $dirCall, $linkHandler, $liHandler) {
                $tree = [];
                Helper::directoryIterator($path, $tree, $fileCall, $dirCall);
                $tree = Helper::sortStringArrayWithHandler(
                    $tree,
                    function (string $v) {
                        return intval(Helper::cutString($v, ['/^-1', '.^0']));
                    }
                );

                $i = 1;
                $masterMenu = [];
                $slaveMenu = [];
                $markdown = [];
                $idMapToKey = [];
                $tree = array_values($tree);
                foreach ($tree as $seq => $file) {
                    $md = $this->parseMdContentAndToc($seq, $file, $linkHandler, $liHandler);
                    $masterMenu[$i] = Helper::dig($md, 'menu');
                    $masterMenu[$i]->setId($i)->setIcon('b:icon-form');
                    if ($anchor = Helper::getAnchor($masterMenu[$i]->getUrl())) {
                        $idMapToKey[$i] = $anchor;
                    }
                    foreach (Helper::dig($md, 'menuSub') as $k => $v) {
                        $key = $i * 1000 + $k + 1;
                        $slaveMenu[$i][] = $v->setId($key)->setIcon('b:icon-pin');
                        if ($ahr = Helper::getAnchor($v->getUrl())) {
                            $idMapToKey[$key] = $ahr;
                        }
                    }
                    $markdown[$file] = $md;
                    $i++;
                }

                return [$markdown, $masterMenu, $slaveMenu, $idMapToKey];
            },
            "md-path-{$path}{$keySuffix}",
            0
        );
    }

    /**
     * Markdown directory parse
     *
     * @param string   $currentFile
     * @param string   $path
     * @param string   $useMenu
     * @param callable $fileCall
     * @param callable $dirCall
     * @param string   $keySuffix
     *
     * @return array
     * @throws Exception
     */
    public function markdownDirectoryParse(
        string $currentFile,
        string $path,
        ?string $useMenu = null,
        ?callable $fileCall = null,
        ?callable $dirCall = null,
        ?string $keySuffix = null
    ) {
        $file = Helper::joinString('/', $path, $currentFile);
        $file = "{$file}.md";

        if (!file_exists($file)) {
            throw new Exception("The document is not found ({$currentFile}.md)");
        }

        [$md, $masterMenu, $slaveMenu, $idMapKey] = $this->parseMdInPath(
            $path,
            $fileCall,
            $dirCall,
            function ($seq, $file, $id, $n, $text) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($n == 1) {
                    $roman = Helper::intToRoman($seq + 1);
                    $text = "{$roman}. {$text}";
                }

                $url = $this->url($this->route, compact('name'));
                $url = "{$url}#{$id}";

                return [$url, $text];
            },
            null,
            md5(implode('+', Helper::multipleToOne(Helper::getDirectoryMd5s($path)))) . $keySuffix
        );

        $openMenu = 0;
        $keyMapId = array_flip($idMapKey);
        foreach ($masterMenu as $master) {
            if (strpos($master->getUrl(), $currentFile) !== false) {
                $openMenu = $master->getId();
            }
        }

        return [
            'toc'          => implode("\n", array_column($md, 'toc')),
            'masterMenu'   => $masterMenu,
            'slaveMenu'    => $slaveMenu,
            'openMenu'     => $openMenu,
            'selectedMenu' => $keyMapId[Helper::getAnchor()] ?? 0,
            'keyMapIdJson' => Helper::jsonStringify($keyMapId),
            'document'     => $md[$file]['content'] ?? '<h2>404 Not Found</h2>',
            'useMenu'      => $useMenu,
            'footer'       => $this->cnf->copyright,
        ];
    }

    /**
     * Get WeChat official account
     *
     * @param array $config
     *
     * @return WxOfficial
     */
    public function wxOfficialCreate(array $config): WxOfficial
    {
        return WxFactory::officialAccount($config);
    }

    /**
     * Get WeChat official account
     *
     * @param string $flag
     *
     * @return WxOfficial
     */
    public function wxOfficial(string $flag = 'default'): WxOfficial
    {
        return $this->wxOfficialCreate($this->parameter("wx_official_{$flag}"));
    }

    /**
     * Get WeChat mini applet
     *
     * @param array $config
     *
     * @return WxApplet
     */
    public function wxAppletCreate(array $config): WxApplet
    {
        return WxFactory::miniProgram($config);
    }

    /**
     * Get WeChat mini applet
     *
     * @param string $flag
     *
     * @return WxApplet
     */
    public function wxApplet(string $flag = 'default'): WxApplet
    {
        return $this->wxAppletCreate($this->parameter("wx_applet_{$flag}"));
    }

    /**
     * Get WeChat payment
     *
     * @param array $config
     * @param bool  $sandbox
     *
     * @return WxPayment
     */
    public function wxPaymentCreate(array $config, bool $sandbox = false): WxPayment
    {
        $sandbox = $sandbox ? ['sandbox' => true] : [];

        return WxFactory::payment(array_merge($config, $sandbox));
    }

    /**
     * Get WeChat payment
     *
     * @param string $flag
     * @param bool   $sandbox
     *
     * @return WxPayment
     */
    public function wxPayment(string $flag = 'default', bool $sandbox = false): WxPayment
    {
        $config = $this->parameter("wx_payment_{$flag}");

        return $this->wxPaymentCreate($config, $sandbox);
    }

    /**
     * Get WeChat open platform
     *
     * @param array $config
     *
     * @return WxOpen
     */
    public function wxOpenPlatformCreate(array $config): WxOpen
    {
        return WxFactory::openPlatform($config);
    }

    /**
     * Get WeChat open platform
     *
     * @param string $flag
     *
     * @return WxOpen
     */
    public function wxOpenPlatform(string $flag = 'default'): WxOpen
    {
        return $this->wxOpenPlatformCreate($this->parameter("wx_open_{$flag}"));
    }

    /**
     * Get WeChat work
     *
     * @param array $config
     *
     * @return WxWork
     */
    public function wxWorkCreate(array $config): WxWork
    {
        return WxFactory::work($config);
    }

    /**
     * Get WeChat work
     *
     * @param string $flag
     *
     * @return WxWork
     */
    public function wxWork(string $flag = 'default'): WxWork
    {
        return $this->wxWorkCreate($this->parameter("wx_work_{$flag}"));
    }

    /**
     * Get WeChat work open platform
     *
     * @param array $config
     *
     * @return WxWorkOpen
     */
    public function wxWorkOpenCreate(array $config): WxWorkOpen
    {
        return WxFactory::openWork($config);
    }

    /**
     * Get WeChat work open platform
     *
     * @param string $flag
     *
     * @return WxWorkOpen
     */
    public function wxWorkOpen(string $flag = 'default'): WxWorkOpen
    {
        return $this->wxWorkOpenCreate($this->parameter("wx_work_open_{$flag}"));
    }

    /**
     * Get WeChat micro merchant
     *
     * @param array $config
     *
     * @return WxMicro
     */
    public function wxMicroCreate(array $config): WxMicro
    {
        return WxFactory::microMerchant($config);
    }

    /**
     * Get WeChat micro merchant
     *
     * @param string $flag
     *
     * @return WxMicro
     */
    public function wxMicro(string $flag = 'default'): WxMicro
    {
        return $this->wxMicroCreate($this->parameter("wx_micro_{$flag}"));
    }

    /**
     * Get ali payment
     *
     * @param string $flag
     * @param bool   $sandbox
     *
     * @return Alipay
     */
    public function aliPayment(string $flag = 'default', bool $sandbox = false): Alipay
    {
        $config = $this->parameter("ali_payment_{$flag}");
        $sandbox = $sandbox ? ['mode' => 'dev'] : [];

        return WxAliPayment::alipay(array_merge($config, $sandbox));
    }
}
