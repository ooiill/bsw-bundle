<?php

namespace Leon\BswBundle\Module\Telegram;

use Leon\BswBundle\Component\Helper;
use Telegram\Bot\Actions;
use Exception;

class IPCommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "ip";

    /**
     * @var string Command Description
     */
    protected $description = "Show ip address.";

    /**
     * @var string
     */
    protected $pattern = "{ip}";

    /**
     * @inheritdoc
     * @return mixed
     */
    public function handle()
    {
        $ip = $this->getArguments()['ip'];
        if (empty($ip)) {
            return $this->textMessage('*Error*: Please given a ip address');
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        try {

            $url = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$ip}&resource_id=6006";
            $data = file_get_contents($url);
            $data = iconv("gbk", "utf-8//IGNORE", $data);

            $data = Helper::parseJsonString($data);
            $data = current($data['data'])['location'] ?? 'pull failed';

        } catch (Exception $e) {
            return $this->textMessage("*Error*: {$e->getMessage()}");
        }

        return $this->textMessage("*Location*：{$data}");
    }
}