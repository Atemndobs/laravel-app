<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\SpotifyController;
use App\Services\Birdy\SpotifyService;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class SpotifyControllerTest.
 *
 * @covers \App\Http\Controllers\Api\SpotifyController
 */
final class SpotifyControllerTest extends TestCase
{
    private SpotifyController $spotifyController;

    private SpotifyService|Mock $spotifyService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->spotifyService = Mockery::mock(SpotifyService::class);
        $this->spotifyController = new SpotifyController($this->spotifyService);
        $this->app->instance(SpotifyController::class, $this->spotifyController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->spotifyController);
        unset($this->spotifyService);
    }

    public function testGetArtistGenre(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
