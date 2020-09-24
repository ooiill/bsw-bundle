<?php

namespace Leon\BswBundle\Module\Telegram;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\DomCrawler\Crawler;
use Telegram\Bot\Actions;

class NewsCommand extends Acme
{
    /**
     * @var string Command Name
     */
    protected $name = "news";

    /**
     * @var int Default limit
     */
    protected $limit = 20;

    /**
     * @var string Command Description
     */
    protected $description = "Show BaiDu news in current day.";

    /**
     * @var string
     */
    protected $pattern = "{limit}";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $limit = $this->getArguments()['limit'];
        if ($limit && $limit > 0 && $limit <= 50) {
            $this->limit = $limit;
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // Logic
        $url = 'http://top.baidu.com/buzz?b=1';
        $newsHtml = file_get_contents($url);
        $crawler = new Crawler($newsHtml);

        $count = 0;
        $news = [];
        $crawler->filterXPath('//table[@class="list-table"]/tr')->each(
            function (Crawler $node, $i) use (&$count, &$news) {

                if ($i < 1) {
                    return;
                }

                if ($count >= $this->limit) {
                    return;
                }

                if ($node->attr('class') == 'item-tr') {
                    return;
                }

                $count += 1;
                $element = $node->filterXPath('//td[@class="keyword"]/a[@class="list-title"]');
                $news[] = [
                    'title' => $element->text(),
                    'href'  => $element->attr('href'),
                    'hot'   => $node->filterXPath('//td[@class="last"]/span')->text(),
                ];
            }
        );

        $date = date(Abs::FMT_DAY);
        $content = "Hot news ({$date})\n\n";

        foreach ($news as $index => $item) {
            $index = str_pad($index + 1, 2, 0, STR_PAD_LEFT);
            $hot = Helper::numberFormat($item['hot'] / 10000, 1, ',') . 'w';
            $content .= "*({$index})* - [{$item['title']}]({$item['href']}) {$hot}\n";
        }

        $this->textMessage($content);
    }
}