<?php

namespace Leon\BswBundle\Module\Telegram;

use Leon\BswBundle\Component\Rsa;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\DomCrawler\Crawler;
use Telegram\Bot\Actions;
use Exception;
use Telegram\Bot\FileUpload\InputFile;

class BeastCommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "beast";

    /**
     * @var string Command Description
     */
    protected $description = "Show your hobbies.";

    /**
     * @var string
     */
    protected $pattern = "{secret}";

    /**
     * Get home url
     *
     * @param string $salt
     *
     * @return string
     */
    public function homeUrl(string $salt): ?string
    {
        $text = "nEmzu/wplxkCAVtQ3{$salt[3]}3BeRx30N2b6FJb5{$salt[3]}ShACDMmkCsjGzv8rWn+MH{$salt[6]}vp0efyhkwsHm1ETRsQUqYyMUwuJMurQe8C{$salt[1]}TtYD9dKW3RQu0{$salt[0]}9m/378SB9WWvNHljEMNyviTFpBOI0saVfnskA5EXl{$salt[4]}sk7tcXv2Ugb5Pq+Avyuf6fjA=";
        $private = realpath('../certificate/rsa_private.pem');
        $public = realpath('../certificate/rsa_public.pem');

        return (new Rsa($private, $public))->decryptByPublicKey($text);
    }

    /**
     * @inheritdoc
     * @return mixed
     */
    public function handle()
    {
        $secret = $this->getArguments()['secret'];
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $telegram = $this->getTelegram();
        $message = $telegram->getWebhookUpdate()->getMessage();

        if (md5($secret) != '69e0f71f25ece4351e4d73af430bec43') {
            return $this->textMessage('*Error*: password error.');
        }

        if ($message->chat->id < 0) {
            return $this->textMessage('*Error*: group messages are not supported.');
        }

        try {
            $homeUrl = $this->homeUrl($secret);
            $homeHtml = file_get_contents($homeUrl);
            $classifyMenu = [];

            $crawler = new Crawler($homeHtml);
            $crawler->filterXPath('//div[@class="model_source"]/ul/li/a')->each(
                function (Crawler $node) use (&$classifyMenu) {
                    $classifyMenu[] = [
                        'name' => $node->text(),
                        'href' => $node->attr('href'),
                    ];
                }
            );

            // classify menu => actor menu
            shuffle($classifyMenu);
            $classifyUrl = $classifyMenu[0]['href'];
            $classifyHtml = file_get_contents($classifyUrl);
            $actorMenu = [];

            $crawler = new Crawler($classifyHtml);
            $crawler->filterXPath('//div[@id="pages"]/a')->each(
                function (Crawler $node) use (&$actorMenu) {
                    if ($node->attr('class')) {
                        return;
                    }
                    $actorMenu[] = intval($node->html());
                }
            );

            // actor menu => actor list
            $actorMenu = array_filter($actorMenu);
            $max = array_pop($actorMenu);
            $actorList = rand(1, $max ?: 1);

            $actorListUrl = $classifyUrl;
            if ($actorList > 1) {
                $actorListUrl = $actorListUrl . $actorList . Abs::HTML_SUFFIX;
            }
            $actorListHtml = file_get_contents($actorListUrl);
            $actorUrl = [];

            $crawler = new Crawler($actorListHtml);
            $crawler->filterXPath('//ul[@class="img"]/li/a')->each(
                function (Crawler $node) use (&$actorUrl) {
                    $actorUrl[] = $node->attr('href');
                }
            );

            // actor list => actor photo
            shuffle($actorUrl);
            $photoUrl = array_pop($actorUrl);
            $photoHtml = file_get_contents($photoUrl);
            $photoMenu = [];

            $crawler = new Crawler($photoHtml);
            $crawler->filterXPath('//div[@id="pages"]/a')->each(
                function (Crawler $node) use (&$photoMenu) {
                    if ($node->attr('class')) {
                        return;
                    }
                    $photoMenu[] = intval($node->html());
                }
            );

            $photoMenu = array_filter($photoMenu);
            $max = array_pop($photoMenu);
            $photoList = rand(1, $max ?: 1);

            // actor photo => photo list
            $photoListUrl = $photoUrl;
            if ($photoList > 1) {
                $photoListUrl = preg_replace('/item\/(\d+)\.html/', "item/\$1_{$photoList}.html", $photoUrl);
            }
            $photoListHtml = file_get_contents($photoListUrl);
            $photoList = [];

            $crawler = new Crawler($photoListHtml);
            $crawler->filterXPath('//div[@class="content"]/center/img')->each(
                function (Crawler $node) use (&$photoList) {
                    $photoList[] = $node->attr('src');
                }
            );

            shuffle($photoList);

            return $this->replyWithPhoto(['photo' => InputFile::create($photoList[0])]);

        } catch (Exception $e) {
            return $this->textMessage("*Error*: {$e->getMessage()}");
        }
    }
}