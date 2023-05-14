<?php


namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;

abstract class AbstractControllerTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;

    protected AbstractBrowser $client;

    public function setUp() : void
    {
        parent::setUp();

        $this->client = static::createClient([], [
            'HTTP_X-API-KEY' => '1234',
            'HTTP_Accept' => 'application/json',
        ]);
        // don't reboot the kernel after one request to avoid trashing of in-memory db
        $this->client->disableReboot();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadAllFixtures();
    }

    public function tearDown() : void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}