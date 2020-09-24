<?php

namespace Leon\BswBundle\Module\Telegram;

use Leon\BswBundle\Component\GoogleAuthenticator;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Telegram\Bot\Actions;

class GoogleCaptchaLoginCommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "gc_login";

    /**
     * @var string Command Description
     */
    protected $description = "Google captcha login for telegram user.";

    /**
     * @var string
     */
    protected $pattern = "{googleCaptcha}";

    /**
     * @inheritdoc
     * @throws
     */
    public function handle()
    {
        $captcha = $this->getArguments()['googleCaptcha'];
        if (empty($captcha)) {
            return $this->textMessage('*Error*: google captcha is required.');
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $telegram = $this->getTelegram();
        $message = $telegram->getWebhookUpdate()->getMessage();

        if ($message->chat->id < 0) {
            return $this->textMessage('*Error*: group messages is not supported.');
        }

        $pdo = $this->pdo();
        $user = $pdo->from('bsw_admin_user')
            ->where('state = ?', 1)
            ->where('telegram_id = ?', $message->from->id)
            ->fetch();
        if (empty($user)) {
            return $this->textMessage('*Error*: sorry, `permission denied`.');
        }

        if (empty($user['google_auth_secret']) || strlen($user['google_auth_secret']) !== 16) {
            return $this->textMessage('*Error*: google auth secret is not configured.');
        }

        $ga = new GoogleAuthenticator();
        $result = $ga->verifyCode($user['google_auth_secret'], $captcha, 2);
        if (!$result) {
            return $this->textMessage('*Error*: google captcha error.');
        }

        $expireMinute = 3;
        $result = $pdo->insertInto(
            'bsw_token',
            [
                'user_id'      => $message->from->id,
                'scene'        => 1,
                'token'        => $token = Helper::generateToken(),
                'expires_time' => time() + Abs::TIME_MINUTE * $expireMinute,
            ]
        )->execute();
        if (empty($result)) {
            return $this->textMessage('*Error*: create token failed.');
        }

        if (empty($host = $_ENV['TG_LOGIN_HOST'] ?? null)) {
            return $this->textMessage('*Error*: configure the `TG_LOGIN_HOST` in env file first.');
        }

        $tips = "Don't publish the link, valid once and in {$expireMinute} minutes.";

        return $this->textMessage("[Doorway here]({$host}?tk4l={$token}) -> `({$tips})`"); // token for login
    }
}