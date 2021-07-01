<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Repository\BswCommandQueueRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class BswMissionCommand extends Command
{
    use BswFoundation;

    /**
     * @return array
     */
    public function args(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'mission',
            'info'    => 'Consumption mission queue',
        ];
    }

    /**
     * Execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var BswCommandQueueRepository $mission
         */
        $missionRepo = $this->repo(BswCommandQueue::class);
        $mission = $missionRepo->lister(
            [
                'limit' => 0,
                'where' => [$this->expr->eq('bcq.state', ':state')],
                'args'  => ['state' => [1]],
                'order' => ['bcq.resourceNeed' => Abs::SORT_ASC],
            ]
        );

        if (!$mission) {
            return;
        }

        $maxId = max(array_column($mission, 'id'));
        foreach ($mission as $key => &$m) {
            if ($m['cronType'] == 2) {
                if (date($m['cronDateFormat']) == $m['cronDateValue']) {
                    $m['level'] = 0 + $m['id'];
                } else {
                    unset($mission[$key]);
                }
            } else {
                $m['level'] = $maxId + $m['id'];
            }
        }

        $queue = [];
        $queueResourceNeed = 0;

        $mission = Helper::sortArray($mission, 'level');
        foreach ($mission as $m) {
            if ($m['cronType'] == 2) {
                array_push($queue, $m);
                $queueResourceNeed += $m['resourceNeed'];
            } elseif ($queueResourceNeed < 30) {
                array_push($queue, $m);
                $queueResourceNeed += $m['resourceNeed'];
            }
        }

        if (!$queue) {
            return $output->writeln("<info>\n None mission in queue after election\n </info>");
        }

        foreach ($queue as $m) {

            $date = date('Y-m-d H:i');
            $condition = $m['condition'] ? Helper::parseJsonString($m['condition']) : [];

            if (!empty($condition['entity'])) {
                $oldSignMd5 = Helper::dig($condition, 'signature');
                if (!Helper::validateSignature($condition, $this->web->parameter('salt'), $oldSignMd5)) {
                    $missionRepo->modify(
                        [Abs::PK => $m['id']],
                        ['state' => 4, 'remark' => "[{$date}] validate signature failed"]
                    );
                    continue;
                }
            }

            Helper::arrayPop($condition, ['time', 'signature']);

            if (!empty($condition['args']) && !is_array($condition['args'])) {
                $args = Helper::jsonArray64($condition['args']);
            }

            if ($m['telegramReceiver']) {
                $condition['receiver'] = $m['telegramReceiver'];
            }

            $condition['args'] = array_merge($args ?? [], $m);

            $conditionHandling = [];
            foreach ($condition as $key => $value) {
                $value = is_array($value) ? Helper::jsonStringify64($value) : $value;
                $conditionHandling[$key] = $value;
            }

            if (isset($condition['receiver'])) {
                $now = date(Abs::FMT_FULL);
                $this->web->telegramSendMessage(
                    $condition['receiver'],
                    "`[{$now}]` Mission running {*id*: `{$m['id']}`, *title*: {$m['title']}}",
                    ['mode' => 'Markdown']
                );
            }

            // begin
            $missionRepo->modify([Abs::PK => $m['id']], ['state' => 2]);

            try {
                $status = $this->web->commandCaller($m['command'], $conditionHandling, $output);
                if ($status === 0) {
                    if ($m['cronReuse']) {
                        $attributes = ['state' => 1, 'donePercent' => 0, 'remark' => "[{$date}] execute success"];
                    } else {
                        $attributes = ['state' => 3];
                    }
                } else {
                    $attributes = ['state' => 4, 'remark' => "[{$date}] exit status: {$status}"];
                }
            } catch (Exception $e) {
                $attributes = ['state' => 4, 'remark' => "[{$date}] {$e->getMessage()}"];
            }

            // end
            $missionRepo->modify([Abs::PK => $m['id']], $attributes);

            // send telegram message
            if (isset($condition['receiver'])) {
                $now = date(Abs::FMT_FULL);
                $this->web->telegramSendMessage(
                    $condition['receiver'],
                    "`[{$now}]` Mission done {*id*: `{$m['id']}`, *title*: {$m['title']}}",
                    ['mode' => 'Markdown']
                );
            }
        }
    }
}