<?php

namespace Tests\Unit\Services\Feeds;

use App\Services\Feeds\ProxyCrawlService;
use Tests\TestCase;

/**
 * Class ProxyCrawlServiceTest.
 *
 * @covers \App\Services\Feeds\ProxyCrawlService
 */
final class ProxyCrawlServiceTest extends TestCase
{
    private ProxyCrawlService $proxyCrawlService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->proxyCrawlService = new ProxyCrawlService();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->proxyCrawlService);
    }

    public function testScrapeSite(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testAbstractApi(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
