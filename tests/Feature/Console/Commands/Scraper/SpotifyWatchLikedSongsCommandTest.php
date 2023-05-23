<?php

namespace Tests\Feature\Console\Commands\Scraper;

use App\Console\Commands\Scraper\SpotifyWatchLikedSongsCommand;
use Tests\TestCase;

/**
 * Class SpotifyWatchLikedSongsCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\SpotifyWatchLikedSongsCommand
 */
final class SpotifyWatchLikedSongsCommandTest extends TestCase
{
    private SpotifyWatchLikedSongsCommand $spotifyWatchLikedSongsCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifyWatchLikedSongsCommand = new SpotifyWatchLikedSongsCommand();
        $this->app->instance(SpotifyWatchLikedSongsCommand::class, $this->spotifyWatchLikedSongsCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyWatchLikedSongsCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:spotify-watch-liked-songs-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
