<?php

namespace Tests\Unit\Services\Scraper;

use App\Services\Scraper\Tools;
use Tests\TestCase;

/**
 * Class ToolsTest.
 *
 * @covers \App\Services\Scraper\Tools
 */
final class ToolsTest extends TestCase
{
    private Tools $tools;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tools = $this->getMockBuilder(Tools::class)
            ->setConstructorArgs([])
            ->getMockForTrait();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->tools);
    }

    public function testDownload(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetSongLinks(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testFilterDownloadUrls(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
