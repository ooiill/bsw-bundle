<?php

namespace Leon\BswBundle\Controller\Traits;

use Carbon\Carbon;
use EasyWeChat\Factory as WxFactory;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Mexitek\PHPColors\Color;
use Yansongda\Pay\Gateways\Alipay;
use Yansongda\Pay\Pay as WxAliPayment;
use EasyWeChat\OfficialAccount\Application as WxOfficial;
use EasyWeChat\Payment\Application as WxPayment;
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
     *
     * @return bool
     */
    public function checkCaptcha(string $input): bool
    {
        $captcha = $this->session->get($this->skCaptcha);
        $this->logger->debug("Number captcha in server {$captcha} and user input {$input}");

        return (new CaptchaBuilder($captcha))->testPhrase($input);
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
     * @param string   $markdownOrFile
     * @param callable $itemsHandler
     *
     * @return array
     */
    public function parseMdContentAndToc(string $markdownOrFile, callable $itemsHandler = null): array
    {
        $parseMarkdown = new Parsedown();
        if (file_exists($markdownOrFile)) {
            $markdown = file_get_contents($markdownOrFile);
        } else {
            $markdown = $markdownOrFile;
        }

        $toc = [];
        $content = $parseMarkdown->text($markdown);
        $content = preg_replace_callback(
            '/\<h([1-3])\>(.*?)\<\/h[1-3]\>/',
            function ($matches) use ($markdownOrFile, $itemsHandler, &$toc) {
                [$_, $n, $text] = $matches;
                $id = strtoupper(substr(Helper::generateToken(), 2, 6));
                $link = "#{$id}";

                if ($itemsHandler) {
                    $items = call_user_func_array($itemsHandler, [$markdownOrFile, $id, $n, $text]);
                    Helper::callReturnType($items, Abs::T_ARRAY, 'Items handler of markdown parser');
                    [$link, $text] = $items;
                }

                $link = Html::tag('a', Html::cleanHtml($text), ['href' => $link]);
                array_push($toc, Html::tag('li', $link, ['class' => ["indent-h{$n} id-{$id}"]]));
                $anchor = Html::tag('a', '♪', ['class' => 'anchor', 'href' => "#{$id}",]);

                return Html::tag("h{$n}", "{$text}{$anchor}", ['id' => $id]);
            },
            $content
        );

        return [
            'toc'     => Html::tag('ul', implode("\n", $toc)),
            'content' => $content,
        ];
    }

    /**
     * Parse markdown in path
     *
     * @param string   $path
     * @param callable $itemsHandler
     *
     * @return array
     */
    public function parseMdInPath(string $path, callable $itemsHandler = null): array
    {
        return $this->caching(
            function () use ($path, $itemsHandler) {
                $tree = [];
                Helper::directoryIterator(
                    $path,
                    $tree,
                    function ($file) {
                        return Helper::strEndWith($file, '.md') ? $file : false;
                    }
                );
                asort($tree);

                $markdown = [];
                foreach ($tree as $file) {
                    $markdown[$file] = $this->parseMdContentAndToc($file, $itemsHandler);
                }

                return $markdown;
            },
            "md-path-{$path}",
            Abs::TIME_WEEK
        );
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
        return WxFactory::officialAccount($this->parameter("wx_official_{$flag}"));
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
        $sandbox = $sandbox ? ['sandbox' => true] : [];

        return WxFactory::payment(array_merge($config, $sandbox));
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