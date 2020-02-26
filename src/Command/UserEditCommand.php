<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserEditCommand extends Command
{
    protected static $defaultName = 'app:user:edit';
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Edits a User')
            ->addArgument('uuid', InputArgument::REQUIRED, 'UUID of User to be edited')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'New EMail Address')
            ->addOption('confirmed', null, InputOption::VALUE_REQUIRED, 'Change emailConfirmed Flag (true/false)')
            ->addOption('superadmin', null, InputOption::VALUE_REQUIRED, 'Change Superadmin Flag (true/false)')
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'Edit Status')
            ->addOption('postcode', null, InputOption::VALUE_REQUIRED, 'Edit Postcode')
            ->addOption('nickname', null, InputOption::VALUE_REQUIRED, 'Edit Nickname')
            ->addOption('firstname', null, InputOption::VALUE_REQUIRED, 'Edit Firstname')
            ->addOption('surname', null, InputOption::VALUE_REQUIRED, 'Edit Surname')
            ->addOption('city', null, InputOption::VALUE_REQUIRED, 'Edit City')
            ->addOption('country', null, InputOption::VALUE_REQUIRED, 'Edit Country')
            ->addOption('phone', null, InputOption::VALUE_REQUIRED, 'Edit Phone')
            ->addOption('gender', null, InputOption::VALUE_REQUIRED, 'Edit Gender');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userdata = $input->getOptions();
        $userdata['uuid'] = $input->getArgument('uuid');

        $user = $this->userService->editUser($userdata);
        if ($user) {
            $io->success("Successfully edited User \"{$user->getEmail()}\" !");
        } else {
            $io->error("Could not edit User \"{$input->getArgument('uuid')}\" !");
        }

        return 0;

        }
}
