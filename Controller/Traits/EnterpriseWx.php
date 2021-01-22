<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Api;
use Monolog\Logger;
use Exception;

/**
 * @property Logger $logger
 */
trait EnterpriseWx
{
    /**
     * Send message to enterprise wx group
     *
     * @param string $receiverToken
     * @param string $message
     *
     * @return string
     * @throws
     */
    public function enterpriseWxSendMessage(string $receiverToken, string $message): ?string
    {
        // tx_enterprise_wx_bot_token
        $host = sprintf($this->parameter('tx_enterprise_wx_hooks_host'), $receiverToken);
        $result = Helper::cURL(
            $host,
            Abs::REQ_POST,
            [
                'msgtype'  => 'markdown',
                'markdown' => [
                    'content'        => $message,
                    'mentioned_list' => ['@all'],
                ],
            ],
            null,
            Abs::CONTENT_TYPE_JSON
        );

        $error = null;
        $result = Helper::parseJsonString($result);
        if ($result['errcode'] > 0) {
            $error = "EnterpriseWxBotError: [{$receiverToken}] {$result['errmsg']}";
            $this->logger->error($error);
        }

        return $error;
    }
}