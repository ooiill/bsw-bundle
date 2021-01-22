<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Api;
use Monolog\Logger;
use Exception;

/**
 * @property Logger $logger
 */
trait Telegram
{
    /**
     * Get telegram bot
     *
     * @param string $tokenKey
     *
     * @return Api
     * @throws
     */
    public function telegram(string $tokenKey = 'telegram_bot_token'): Api
    {
        $telegram = new Api($this->parameter($tokenKey));
        $telegram->setTimeOut($this->cnf->curl_timeout_second);

        return $telegram;
    }

    /**
     * Send anything to telegram users
     *
     * @param string|array $receiver
     * @param callable     $logic
     * @param array        $params
     * @param Api|null     $telegram
     *
     * @return array
     */
    public function telegramSendAny($receiver, callable $logic, array $params = [], ?Api $telegram = null): array
    {
        if (!is_array($receiver)) {
            $receiver = Helper::stringToArray($receiver, true, true, 'intval');
        }

        $error = [];
        $telegram = $telegram ?? $this->telegram();

        foreach ($receiver as $user) {
            try {
                $params = array_merge(['chat_id' => $user, 'parse_mode' => 'Markdown'], $params);
                call_user_func_array($logic, [$telegram, $params]);
            } catch (Exception $e) {
                $message = "TelegramBotError: [{$user}] {$e->getMessage()}";
                $this->logger->error($message);
                array_push($error, $message);
            }
        }

        return [$error, $receiver];
    }

    /**
     * Send message to telegram users
     *
     * @param string|array $receiver
     * @param string       $message
     * @param array        $params
     * @param Api|null     $telegram
     *
     * @return array
     */
    public function telegramSendMessage($receiver, string $message, array $params = [], ?Api $telegram = null): array
    {
        return $this->telegramSendAny(
            $receiver,
            function (Api $telegram, array $params) use ($message) {
                $telegram->sendMessage(array_merge($params, ['text' => $message]));
            },
            $params,
            $telegram
        );
    }

    /**
     * Send document to telegram users
     *
     * @param string|array $receiver
     * @param string       $file
     * @param array        $params
     * @param Api|null     $telegram
     *
     * @return array
     */
    public function telegramSendDocument($receiver, string $file, array $params = [], ?Api $telegram = null): array
    {
        return $this->telegramSendAny(
            $receiver,
            function (Api $telegram, array $params) use ($file) {
                $telegram->sendDocument(array_merge($params, ['document' => new InputFile($file)]));
            },
            $params,
            $telegram
        );
    }
}