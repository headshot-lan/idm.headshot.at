<?php

namespace App\Command;

use App\Service\ApiKeyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ApikeysListCommand extends Command
{
    protected static $defaultName = 'app:apikeys:list';
    /**
     * @var ApiKeyService
     */
    private $apiKeyService;

    public function __construct(ApiKeyService $apiKeyService)
    {
        $this->apiKeyService = $apiKeyService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Lists all API Keys')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $keys = $this->apiKeyService->listApiKeys();

        $table = new Table($output);
        $table->setHeaders(['Name', 'API Key', 'Host']);
        foreach ($keys as $key) {
            $table->addRow([$key->getName(), $key->getApiToken(), $key->getHost()]);
        }
        $table->render();

        return 0;
    }
}
