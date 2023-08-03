<?php

namespace Tests\Feature\Console\Commands\Song;

use App\Console\Commands\Song\SpotifyUpdateSongBySpotifyID;
use Tests\TestCase;

/**
 * Class SpotifyUpdateSongBySpotifyIDTest.
 *
 * @covers \App\Console\Commands\Song\SpotifyUpdateSongBySpotifyID
 */
final class SpotifyUpdateSongBySpotifyIDTest extends TestCase
{
    private SpotifyUpdateSongBySpotifyID $spotifyUpdateSongBySpotifyID;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifyUpdateSongBySpotifyID = new SpotifyUpdateSongBySpotifyID();
        $this->app->instance(SpotifyUpdateSongBySpotifyID::class, $this->spotifyUpdateSongBySpotifyID);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyUpdateSongBySpotifyID);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:spotify-update-song-by-spotify-i-d')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
