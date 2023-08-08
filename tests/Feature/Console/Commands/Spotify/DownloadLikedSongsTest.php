<?php

namespace Tests\Feature\Console\Commands\Spotify;

use App\Console\Commands\Spotify\DownloadLikedSongs;
use Tests\TestCase;

/**
 * Class DownloadLikedSongsTest.
 *
 * @covers \App\Console\Commands\Spotify\DownloadLikedSongs
 */
final class DownloadLikedSongsTest extends TestCase
{
    private DownloadLikedSongs $downloadLikedSongs;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->downloadLikedSongs = new DownloadLikedSongs();
        $this->app->instance(DownloadLikedSongs::class, $this->downloadLikedSongs);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->downloadLikedSongs);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:download-liked-songs')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
