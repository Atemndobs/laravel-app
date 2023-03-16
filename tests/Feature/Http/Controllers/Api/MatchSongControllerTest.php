<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\MatchSongController;
use App\Models\Song;
use App\Services\Birdy\BirdyMatchService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class MatchSongControllerTest.
 *
 * @covers \App\Http\Controllers\Api\MatchSongController
 */
final class MatchSongControllerTest extends TestCase
{
    private MatchSongController $matchSongController;

    private Request|Mock $request;

    private Song|Mock $song;

    private BirdyMatchService|Mock $birdyMatchService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->request = Mockery::mock(Request::class);
        $this->song = Mockery::mock(Song::class);
        $this->birdyMatchService = Mockery::mock(BirdyMatchService::class);
        $this->matchSongController = new MatchSongController($this->request, $this->song, $this->birdyMatchService);
        $this->app->instance(MatchSongController::class, $this->matchSongController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->matchSongController);
        unset($this->request);
        unset($this->song);
        unset($this->birdyMatchService);
    }

    public function testGetSongMatch(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }

    public function testMatchByAttribute(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
