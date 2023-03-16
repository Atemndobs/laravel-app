<?php

namespace Tests\Feature\Console\Commands\Scraper;

use App\Console\Commands\Scraper\ScrapeMusicCommand;
use Tests\TestCase;

/**
 * Class ScrapeMusicCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\ScrapeMusicCommand
 */
final class ScrapeMusicCommandTest extends TestCase
{
    private ScrapeMusicCommand $scrapeMusicCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->scrapeMusicCommand = new ScrapeMusicCommand();
        $this->app->instance(ScrapeMusicCommand::class, $this->scrapeMusicCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->scrapeMusicCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('scrape:song {site?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }

    public function testExtracted(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testExtractSongTitleFromLink(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testDownloadMany(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
