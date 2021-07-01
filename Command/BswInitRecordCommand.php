<?php

namespace Leon\BswBundle\Command;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\CommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BswInitRecordCommand extends Command
{
    use BswFoundation;

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'doctrine'       => [null, InputOption::VALUE_OPTIONAL, 'Doctrine database flag'],
            'force'          => [null, InputOption::VALUE_OPTIONAL, 'Force init record again', 'no'],
            'admin-phone'    => [null, InputOption::VALUE_OPTIONAL, 'Admin phone number', '17011223344'],
            'admin-name'     => [null, InputOption::VALUE_OPTIONAL, 'Admin name', 'Master'],
            'admin-password' => [null, InputOption::VALUE_REQUIRED, 'Admin password', 'bsw@2020#BSW'],
        ];
    }

    /**
     * @return array
     */
    public function base(): array
    {
        return [
            'prefix'  => 'bsw',
            'keyword' => 'init-record',
            'info'    => 'Project initialization record',
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
        $params = $this->options($input);
        $project = $this->kernel->getProjectDir();

        $doneFile = "{$project}/.done-init-record";
        if ($params['force'] !== 'yes' && file_exists($doneFile)) {
            throw new CommandException('The command can only be executed once');
        }

        $pdo = $this->pdo($params['doctrine'] ?: 'default');

        // bsw_admin_user

        $pdo->exec('TRUNCATE bsw_admin_user');
        $pdo->insert(
            'bsw_admin_user',
            [
                'phone'    => $params['admin-phone'],
                'name'     => $params['admin-name'],
                'password' => $this->web->password($params['admin-password']),
            ]
        );

        // bsw_admin_menu

        $menu = [
            [0, '', 'b:icon-set', 'Backend manager', 99, 1],
            [1, 'app_bsw_admin_menu_preview', 'b:icon-navlist', 'Bsw admin menu', 9901, 1],
            [1, 'app_bsw_config_preview', 'b:icon-form', 'Project config', 9902, 1],
            [1, 'app_bsw_admin_role_preview', 'b:icon-bussinessman', 'Bsw admin role', 9903, 1],
            [1, 'app_bsw_admin_user_preview', 'b:icon-atm', 'Bsw admin user', 9904, 1],
            [1, 'app_bsw_admin_login_preview', 'b:icon-online-tracking', 'Bsw admin login', 9905, 1],
            [1, 'app_bsw_admin_persistence_log_preview', 'b:icon-history', 'Bsw admin persistence log', 9906, 1],
            [1, 'app_bsw_captcha_preview', 'b:icon-assessedbadge', 'Bsw captcha', 9907, 0],
            [1, 'app_bsw_attachment_preview', 'b:icon-icon-57', 'Bsw attachment', 9908, 1],
            [1, 'app_bsw_command_queue_preview', 'b:icon-process', 'Bsw command queue', 9909, 1],
            [1, 'app_bsw_token_preview', 'b:icon-qianming', 'Bsw token', 9910, 0],
            [1, 'app_enum_dict', 'b:icon-office', 'Bsw enum dict', 9911, 1],
        ];

        $pdo->exec('TRUNCATE bsw_admin_menu');
        foreach ($menu as $item) {
            $value = array_combine(['menu_id', 'route_name', 'icon', 'value', 'sort', 'state'], $item);
            $pdo->insert('bsw_admin_menu', $value);
        }

        file_put_contents($doneFile, date(Abs::FMT_FULL));
        $output->writeln("<info>\n Project record initialization done.\n </info>");
    }
}