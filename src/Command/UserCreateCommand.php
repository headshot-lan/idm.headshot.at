<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';
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
            ->setDescription('Creates a User')
            ->addArgument('email', InputArgument::REQUIRED, 'EMail')
            ->addArgument('password', InputArgument::REQUIRED, 'Plaintext Password')
            ->addArgument('nickname', InputArgument::REQUIRED, 'Nickname')
            ->addOption('confirmed', null, InputOption::VALUE_NONE, 'Set emailConfirmed to true')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userdata = [
            'email' => $input->getArgument('email'),
            'password' => $input->getArgument('password'),
            'nickname' => $input->getArgument('nickname'),
        ];
        if ($input->getOption('confirmed')) {
            $userdata['confirmed'] = true;
        } else {
            $userdata['confirmed'] = false;
        }
        $result = $this->userService->createUser($userdata);

        if ($result) {
            $io->success("User \"{$result->getEmail()}\" successfully created!");
        } else {
            $io->error("User \"{$input->getArgument('email')}\" could not be created!");
        }

        return 0;
    }
}
