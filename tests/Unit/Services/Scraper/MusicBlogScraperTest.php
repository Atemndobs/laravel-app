<?php

namespace Tests\Unit\Services\Scraper;

use App\Services\Scraper\MusicBlogScraper;
use Tests\TestCase;

/**
 * Class MusicBlogScraperTest.
 *
 * @covers \App\Services\Scraper\MusicBlogScraper
 */
final class MusicBlogScraperTest extends TestCase
{
    private MusicBlogScraper $musicBlogScraper;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->musicBlogScraper = new MusicBlogScraper();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->musicBlogScraper);
    }

    public function testGetNotJustOKAudio(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetMusicFromSource(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
