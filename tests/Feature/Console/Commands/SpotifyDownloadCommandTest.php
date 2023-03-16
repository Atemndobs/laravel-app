<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\Scraper\SpotifyDownloadCommand;
use Tests\TestCase;

/**
 * Class SpotifyDownloadCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\SpotifyDownloadCommand
 */
final class SpotifyDownloadCommandTest extends TestCase
{
    private SpotifyDownloadCommand $spotifyDownloadCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifyDownloadCommand = new SpotifyDownloadCommand();
        $this->app->instance(SpotifyDownloadCommand::class, $this->spotifyDownloadCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyDownloadCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('command:name')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
