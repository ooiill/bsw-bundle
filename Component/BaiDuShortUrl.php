<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Entity\Abs;

class BaiDuShortUrl
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $url = 'https://dwz.cn/admin/v2/create';

    /**
     * BaiDuSDK constructor.
     *
     * @param string $token
     * @param string $url
     */
    public function __construct(string $token, string $url = null)
    {
        $this->token = $token;
        if ($url) {
            $this->url = $url;
        }
    }

    /**
     * Get short url
     *
     * @param string $url
     * @param int    $second
     *
     * @return string
     * @throws
     */
    public function shortUrl(string $url, int $second = 30): string
    {
        $result = Helper::cURL(
            $this->url,
            Abs::REQ_POST,
            null,
            function (array $options) use ($url, $second) {
                $options[CURLOPT_HTTPHEADER] = [
                    'Content-Type: application/json',
                    "Token: {$this->token}",
                ];
                $options[CURLOPT_POSTFIELDS] = Helper::jsonStringify(['url' => $url]);
                $options[CURLOPT_TIMEOUT_MS] = $second * 1000;

                return $options;
            }
        );

        $result = Helper::parseJsonString($result);
        if (!$result || ($result['Code'] ?? true) !== 0) {
            return $url;
        }

        return $result['ShortUrl'] ?? $url;
    }
}