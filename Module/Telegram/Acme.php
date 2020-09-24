<?php

namespace Leon\BswBundle\Module\Telegram;

use Telegram\Bot\Commands\Command;
use Envms\FluentPDO\Query as MysqlClient;
use PDO;

abstract class Acme extends Command
{
    /**
     * Reply text message
     *
     * @param string $message
     *
     * @return mixed
     */
    protected function textMessage(string $message)
    {
        return $this->replyWithMessage(
            [
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]
        );
    }

    /**
     * Get pdo instance
     *
     * @return MysqlClient
     */
    protected function pdo(): MysqlClient
    {
        $config = parse_url($_ENV['DATABASE_URL']);
        $dbname = trim($config['path'], '/');

        $pdo = new PDO(
            "mysql:dbname={$dbname};host={$config['host']};port={$config['port']}",
            $config['user'],
            $config['pass']
        );

        $fluent = new MysqlClient($pdo);
        $fluent->exceptionOnError = true;

        return $fluent;
    }
}