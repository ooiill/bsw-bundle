<?php

namespace Leon\BswBundle\Module\Telegram;

use Telegram\Bot\Actions;

class WhoAmICommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "whoami";

    /**
     * @var string Command Description
     */
    protected $description = "Show your id, name, username and group id.";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $telegram = $this->getTelegram();
        $message = $telegram->getWebhookUpdate()->getMessage();

        $caption = sprintf(
            '*Your Id*: %d' . PHP_EOL .
            '*Group Id*: %d' . PHP_EOL .
            '*Name*: %s %s' . PHP_EOL .
            '*Username*: %s',
            $message->from->id,
            $message->chat->id,
            $message->from->firstName,
            $message->from->lastName,
            $message->from->username
        );

        $this->textMessage($caption);
    }
}