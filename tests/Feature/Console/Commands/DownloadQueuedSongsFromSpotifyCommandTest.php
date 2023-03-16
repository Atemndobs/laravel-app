<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Scraper\DownloadQueuedSongsFromSpotifyCommand;
use Tests\TestCase;

/**
 * Class DownloadQueuedSongsFromSpotifyCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\DownloadQueuedSongsFromSpotifyCommand
 */
final class DownloadQueuedSongsFromSpotifyCommandTest extends TestCase
{
    private DownloadQueuedSongsFromSpotifyCommand $downloadQueuedSongsFromSpotifyCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->downloadQueuedSongsFromSpotifyCommand = new DownloadQueuedSongsFromSpotifyCommand();
        $this->app->instance(DownloadQueuedSongsFromSpotifyCommand::class, $this->downloadQueuedSongsFromSpotifyCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->downloadQueuedSongsFromSpotifyCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
