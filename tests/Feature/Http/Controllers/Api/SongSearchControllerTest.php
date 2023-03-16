<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\SongSearchController;
use App\Models\Song;
use App\Services\Birdy\MeiliSearchService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class SongSearchControllerTest.
 *
 * @covers \App\Http\Controllers\Api\SongSearchController
 */
final class SongSearchControllerTest extends TestCase
{
    private SongSearchController $songSearchController;

    private Request|Mock $request;

    private Song|Mock $song;

    private MeiliSearchService|Mock $meiliSearchService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->request = Mockery::mock(Request::class);
        $this->song = Mockery::mock(Song::class);
        $this->meiliSearchService = Mockery::mock(MeiliSearchService::class);
        $this->songSearchController = new SongSearchController($this->request, $this->song, $this->meiliSearchService);
        $this->app->instance(SongSearchController::class, $this->songSearchController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->songSearchController);
        unset($this->request);
        unset($this->song);
        unset($this->meiliSearchService);
    }

    public function testSearchSong(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
