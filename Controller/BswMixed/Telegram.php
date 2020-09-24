<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Api;
use App\Kernel;
use Exception;

/**
 * @property Kernel $kernel
 */
trait Telegram
{
    /**
     * Telegram bot - debug
     *
     * @Route("/tg/debug", name="app_tg_debug")
     *
     * @O("bot", type="string", label="Bot name")
     * @O("mode", type="string", label="Mode")
     * @O("updates", type="object[]", label="Updates")
     * @O("commands", type="array", label="Bot commands")
     *
     * @throws
     */
    public function getTgDebugAction()
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        /**
         * @var Api $telegram
         */
        $telegram = $this->telegram();
        $user = $telegram->getMe();

        $update = [];
        $isWebHook = false;

        try {
            $update = $telegram->getUpdates(['limit' => 5]);
        } catch (Exception $e) {
            $isWebHook = true;
        }

        return $this->okayAjax(
            [
                'bot'      => "{$user->get('first_name')}({$user->get('username')})",
                'mode'     => $isWebHook ? 'WebHooks' : 'Normal',
                'updates'  => $isWebHook ? $telegram->getWebhookUpdate()->all() : $update,
                'commands' => $telegram->getCommands(),
            ],
            'Just debug telegram bot.'
        );
    }

    /**
     * Telegram bot - web hooks
     *
     * @Route("/tg/hooks", name="app_tg_hooks")
     *
     * @O("remove_result", type="bool", label="Result of delete web hook")
     * @O("set_params", type="array", label="Web hook params")
     * @O("set_result", type="bool", label="Result of set web hook")
     *
     * @throws
     */
    public function getTgSetHooksAction()
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        /**
         * @var Api $telegram
         */
        $telegram = $this->telegram();
        $params = ['url' => "{$this->parameter('telegram_hooks_host')}/tg/cmd"];

        return $this->okayAjax(
            [
                'remove_result' => $telegram->removeWebhook(),
                'set_params'    => $params,
                'set_result'    => $telegram->setWebhook($params),
            ],
            'Telegram bot web hooked done.'
        );
    }

    /**
     * Fetch commands
     *
     * @param string $path
     * @param string $namespace
     *
     * @return array
     */
    protected function fetchCommands(string $path, string $namespace): array
    {
        $commands = [];
        Helper::directoryIterator(
            $path,
            $commands,
            function ($_, $item) use ($path,$namespace) {

                if (!Helper::strEndWith($item, 'Command.php')) {
                    return false;
                }

                $command = str_replace('.php', null, $item);
                $command = "{$namespace}\\{$command}";

                return new $command;
            }
        );

        return $commands;
    }

    /**
     * Telegram bot - commands
     *
     * @Route("/tg/cmd", name="app_tg_cmd", methods={"GET", "POST"})
     *
     * @O("ok", type="bool", label="Is ok for add commands")
     * @O("total", type="int", label="Total commands")
     *
     * @return Response
     * @throws
     */
    public function postTgCmdAction()
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $bswPath = $this->kernel->getBundle(Abs::BSW_BUNDLE)->getPath();
        $bswCommands = $this->fetchCommands("{$bswPath}/Module/Telegram", "Leon\\BswBundle\\Module\\Telegram");

        $appPath = $this->kernel->getProjectDir();
        $appCommands = $this->fetchCommands("{$appPath}/src/Module/Telegram", "App\\Module\\Telegram");

        $commands = array_merge($bswCommands, $appCommands);

        /**
         * @var Api $telegram
         */
        $telegram = $this->telegram();
        $telegram->addCommands($commands);
        $telegram->commandsHandler(true);

        return $this->okayAjax(
            [
                'ok'    => !empty($commands),
                'total' => count($commands),
            ],
            'Telegram bot commands hooked done.'
        );
    }
}