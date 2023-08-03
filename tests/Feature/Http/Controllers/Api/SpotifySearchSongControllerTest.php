<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\SpotifySearchSongController;
use Tests\TestCase;

/**
 * Class SpotifySearchSongControllerTest.
 *
 * @covers \App\Http\Controllers\Api\SpotifySearchSongController
 */
final class SpotifySearchSongControllerTest extends TestCase
{
    private SpotifySearchSongController $spotifySearchSongController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->spotifySearchSongController = new SpotifySearchSongController();
        $this->app->instance(SpotifySearchSongController::class, $this->spotifySearchSongController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifySearchSongController);
    }

    public function testSearchSong(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
