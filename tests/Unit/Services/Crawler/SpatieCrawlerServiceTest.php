<?php

namespace Tests\Unit\Services\Crawler;

use App\Services\Crawler\SpatieCrawlerService;
use Goutte\Client as ClientAlias;
use GuzzleHttp\Client;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class SpatieCrawlerServiceTest.
 *
 * @covers \App\Services\Crawler\SpatieCrawlerService
 */
final class SpatieCrawlerServiceTest extends TestCase
{
    private SpatieCrawlerService $spatieCrawlerService;

    private Client|Mock $client;

    private ClientAlias|Mock $goutte;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(Client::class);
        $this->goutte = Mockery::mock(ClientAlias::class);
        $this->spatieCrawlerService = new SpatieCrawlerService($this->client, $this->goutte);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spatieCrawlerService);
        unset($this->client);
        unset($this->goutte);
    }

    public function testCrawlUrl(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetPage(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
