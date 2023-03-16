<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\UploadController;
use App\Models\Song;
use App\Services\Strapi\StrapiSongService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class UploadControllerTest.
 *
 * @covers \App\Http\Controllers\Api\UploadController
 */
final class UploadControllerTest extends TestCase
{
    private UploadController $uploadController;

    private UploadService|Mock $uploadService;

    private Song|Mock $song;

    private Request|Mock $request;

    private StrapiSongService|Mock $strapiSongService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->uploadService = Mockery::mock(UploadService::class);
        $this->song = Mockery::mock(Song::class);
        $this->request = Mockery::mock(Request::class);
        $this->strapiSongService = Mockery::mock(StrapiSongService::class);
        $this->uploadController = new UploadController($this->uploadService, $this->song, $this->request, $this->strapiSongService);
        $this->app->instance(UploadController::class, $this->uploadController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->uploadController);
        unset($this->uploadService);
        unset($this->song);
        unset($this->request);
        unset($this->strapiSongService);
    }

    public function testUpload(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }

    public function testGetStrapiUploads(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }

    public function testStrapiUploadsWebhook(): void
    {
        /** @todo This test is incomplete. */
        $this->getJson('/path')
            ->assertStatus(200);
    }
}
