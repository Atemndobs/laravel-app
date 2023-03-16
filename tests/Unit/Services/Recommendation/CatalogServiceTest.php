<?php

namespace Tests\Unit\Services\Recommendation;

use App\Services\Recommendation\CatalogService;
use Tests\TestCase;

/**
 * Class CatalogServiceTest.
 *
 * @covers \App\Services\Recommendation\CatalogService
 */
final class CatalogServiceTest extends TestCase
{
    private CatalogService $catalogService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->catalogService = new CatalogService();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->catalogService);
    }

    public function testConvertSongToCatalog(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testCreatCatalog(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testExportCatalog(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testCreateUsageData(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
