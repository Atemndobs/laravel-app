<?php

namespace Tests\Feature\Console\Commands\Scraper;

use App\Console\Commands\Scraper\SpotifyLikedSongsImportCommand;
use Tests\TestCase;

/**
 * Class SpotifyLikedSongsImportCommandTest.
 *
 * @covers \App\Console\Commands\Scraper\SpotifyLikedSongsImportCommand
 */
final class SpotifyLikedSongsImportCommandTest extends TestCase
{
    private SpotifyLikedSongsImportCommand $spotifyLikedSongsImportCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifyLikedSongsImportCommand = new SpotifyLikedSongsImportCommand();
        $this->app->instance(SpotifyLikedSongsImportCommand::class, $this->spotifyLikedSongsImportCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyLikedSongsImportCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:spotify-liked-songs-import-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
